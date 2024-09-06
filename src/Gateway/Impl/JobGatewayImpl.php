<?php

namespace Teamone\DispatchClient\Gateway\Impl;

use GuzzleHttp\Exception\RequestException;
use Teamone\DispatchClient\Exceptions\InvalidArgumentException;
use Teamone\DispatchClient\Exceptions\TeamoneDispatchClientException;
use Teamone\DispatchClient\Gateway\Chain\AbstractResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ContentErrorResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ContentStatusResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ContentStructureResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ContentTypeResponseChain;
use Teamone\DispatchClient\Gateway\Chain\ConvertArrayResponseChain;
use Teamone\DispatchClient\Gateway\Chain\HttpStatusResponseChain;
use Teamone\DispatchClient\Gateway\GatewayRequest;
use Teamone\DispatchClient\Gateway\JobGateway;

class JobGatewayImpl extends GatewayRequest implements JobGateway
{
    /**
     * @desc 在project的flow下新增command job
     * @param array $params
     * params.project 项目名称
     * params.flowName flow的名称
     * params.jobName 需要添加到flow下的job的名称
     * params.type 需要添加到flow下的job的类型（command）
     * params.command 需要添加到flow下的job的具体command内容
     * params.dependOn 需要添加到flow下的job的依赖的job名称，多个以逗号隔开
     * @return array
     */
    public function addJobInCurrentFlow(string $sessionId, string $project, string $flowName, string $jobName, string $command, string $dependOn = ""): array
    {
        $valid = true;

        $json = [
            'session.id'             => $sessionId ?: ($valid = null),
            'project'                => $project ?: ($valid = null),
            'flowName'               => $flowName ?: ($valid = null),
            'newJobProp[newJobName]' => $jobName ?: ($valid = null),
            'newJobProp[type]'       => 'command',
            // 需要添加到flow下的job的具体command内容，如 echo test-zxj_command
            'newJobProp[command]'    => $command ?: ($valid = null),
        ];

        if (isset($dependOn) && !empty($dependOn)) {
            $json['newJobProp[dependOn]'] = $dependOn;
        }

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/manager?ajax=addJobInCurrentFlow';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['projectId', 'project'];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            $ret = [
                'projectId' => $contents['projectId'],
                'project'   => $contents['project'],
            ];

            return $ret;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 在project的flow下新增http job
     * @param array $params
     * @return array
     */
    public function addJobInCurrentFlowHttp(array $params): array
    {
        $valid = true;

        $params['protocol'] = $params['protocol'] ?? 'http';

        $json = [
            'session.id'                                      => $params['sessionId'] ?? ($valid = null),
            // 项目名称
            'project'                                         => $params['project'] ?? ($valid = null),
            // flow的名称
            'flowName'                                        => $params['flowName'] ?? ($valid = null),
            // 需要添加到flow下的job的名称
            'newJobProp[newJobName]'                          => $params['jobName'] ?? ($valid = null),
            // 需要添加到flow下的job的类型（http）
            'newJobProp[type]'                                => $params['protocol'] ?? ($valid = null),
            // 需要添加到flow下的job的依赖的job名称，多个以逗号隔开
            'newJobProp[dependOn]'                            => $params['dependOn'] ?? "",
            // http_job类型的job特有，请求的url
            'newJobProp[http_job.request.url]'                => $params['requestUrl'] ?? "",
            // http_job类型的job特有，请求的方式
            'newJobProp[http_job.request.method]'             => $params['requestMethod'] ?? "",
            // http_job类型的job特有，请求的参数类型
            'newJobProp[http_job.request.content.type]'       => $params['requestContentType'] ?? "application/json",
            // http_job类型的job特有，请求的参数（form风格或者json风格）
            'newJobProp[http_job.request.param]'              => $params['requestParam'] ?? [],
            // 请求的特有参数，用于标识请求得到的响应应该取哪部分数据作为参数传递给回调，默认为 “data”
            'newJobProp[http_job.request.callback.param.key]' => $params['requestCallbackParamKey'] ?? "",
            // 请求的超时时间，单位为秒，默认为 3600
            'newJobProp[http_job.request.timeout]'            => $params['requestTimeout'] ?? "3600",
            // http_job类型的job特有，用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
            'newJobProp[http_job.request.code]'               => $params['requestCode'] ?? "999",
            // http_job类型的job特有，请求是否需要鉴token，如果需要传 1 ，默认不需要 0
            'newJobProp[http_job.request.needToken]'          => $params['requestNeedToken'] ?? "0",
            // http_job类型的job特有，回调的url
            'newJobProp[http_job.callback.url]'               => $params['callbackUrl'] ?? "",
            // http_job类型的job特有，回调的方式
            'newJobProp[http_job.callback.method]'            => $params['callbackMethod'] ?? "",
            // http_job类型的job特有，回调的参数类型
            'newJobProp[http_job.callback.content.type]'      => $params['contentType'] ?? "",
            // http_job类型的job特有，回调的参数（form风格或者json风格）
            'newJobProp[http_job.callback.param]'             => $params['callbackParam'] ?? "",
            // 回调的超时时间，单位为秒，默认为 3600
            'newJobProp[http_job.callback.timeout]'           => $params['callbackTimeout'] ?? "3600",
            // 用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
            'newJobProp[http_job.callback.code]'              => $params['callbackCode'] ?? "999",
            // http_job类型的job特有，回调是否需要鉴token，如果需要传 1 ，默认不需要 0
            'newJobProp[http_job.callback.needToken]'         => $params['callbackNeedToken'] ?? "0",
        ];

        if (!in_array($json['newJobProp[type]'], ['http', 'https'])) {
            throw new InvalidArgumentException("Not support protocol: {$params['protocol']}");
        }

        if ($json["newJobProp[http_job.request.content.type]"] === "application/json") {
            $json["newJobProp[http_job.request.param]"] = json_encode($json["newJobProp[http_job.request.param]"]);
            if (json_last_error()) {
                throw new InvalidArgumentException('RequestParam Error.');
            }
        }

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/manager?ajax=addJobInCurrentFlow';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['projectId', 'project'];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            $ret = [
                'projectId' => $contents['projectId'],
                'project'   => $contents['project'],
            ];

            return $ret;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 在project的flow下删除多个job
     * @param string $sessionId
     * @param string $project 项目名称
     * @param string $flowName flow的名称
     * @param string $deleteJobNames 需要删除的job的名称，多个job的话以逗号分隔传入
     * @return array
     */
    public function deleteJobsInCurrentFlow(string $sessionId, string $project, string $flowName, string $deleteJobNames): array
    {

        $valid = true;
        $json  = [
            'session.id'     => $sessionId ?? ($valid = null),
            'project'        => $project ?? ($valid = null),
            'flowName'       => $flowName ?? ($valid = null),
            'deleteJobNames' => $deleteJobNames ?? ($valid = null),
        ];
        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/manager?ajax=deleteJobsInCurrentFlow';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['projectId', 'project'];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            $ret = [
                'projectId' => $contents['projectId'],
                'project'   => $contents['project'],
            ];

            return $ret;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 在project的flow下修改command job
     * @param array $params
     * @return array
     */
    public function setJobOverrideProperty(string $sessionId, string $project, string $flowName, string $jobName, string $command): array
    {
        try {
            $json = [
                'session.id'           => $sessionId,
                // 项目名称
                'project'              => $project,
                // flow的名称
                'flowName'             => $flowName,
                // 需要添加到flow下的job的名称
                'jobName'              => $jobName,
                // 需要修改到flow下的job的类型（command）
                'jobOverride[type]'    => 'command',
                // 需要修改到flow下的job的具体command内容
                'jobOverride[command]' => $command,
            ];

            $uri      = '/manager?ajax=setJobOverrideProperty';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['projectId', 'project'];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            return $contents;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 在project的flow下修改http job
     * @param array $params
     * @return array
     */
    public function setJobOverridePropertyHttp(array $params): array
    {
        try {
            $json = [
                'session.id'                                      => $params['sessionId'],
                // 项目名称
                'project'                                         => $params['project'],
                // flow的名称
                'flowName'                                        => $params['flowName'],
                // 需要添加到flow下的job的名称
                'newJobProp[newJobName]'                          => $params['jobName'],
                // 需要添加到flow下的job的类型（http）
                'newJobProp[type]'                                => 'http',
                // 需要添加到flow下的job的依赖的job名称，多个以逗号隔开
                'newJobProp[dependOn]'                            => $params['dependOn'] ?? "",
                // http_job类型的job特有，请求的url
                'newJobProp[http_job.request.url]'                => $params['requestUrl'] ?? "",
                // http_job类型的job特有，请求的方式
                'newJobProp[http_job.request.method]'             => $params['requestMethod'] ?? "",
                // http_job类型的job特有，请求的参数类型
                'newJobProp[http_job.request.content.type]'       => $params['requestContentType'] ?? "",
                // http_job类型的job特有，请求的参数（form风格或者json风格）
                'newJobProp[http_job.request.param]'              => $params['requestParam'] ?? "",
                // 请求的特有参数，用于标识请求得到的响应应该取哪部分数据作为参数传递给回调，默认为 “data”
                'newJobProp[http_job.request.callback.param.key]' => $params['requestCallbackParamKey'] ?? "",
                // 请求的超时时间，单位为秒，默认为 3600
                'newJobProp[http_job.request.timeout]'            => $params['requestTimeout'] ?? "",
                // http_job类型的job特有，用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
                'newJobProp[http_job.request.code]'               => $params['requestCode'] ?? "",
                // http_job类型的job特有，请求是否需要鉴token，如果需要传 1 ，默认不需要 0
                'newJobProp[http_job.request.needToken]'          => $params['requestNeedToken'] ?? "",
                // http_job类型的job特有，回调的url
                'newJobProp[http_job.callback.url]'               => $params['callbackUrl'] ?? "",
                // http_job类型的job特有，回调的方式
                'newJobProp[http_job.callback.method]'            => $params['callbackMethod'] ?? "",
                // http_job类型的job特有，回调的参数类型
                'newJobProp[http_job.callback.content.type]'      => $params['contentType'] ?? "",
                // http_job类型的job特有，回调的参数（form风格或者json风格）
                'newJobProp[http_job.callback.param]'             => $params['callbackParam'] ?? "",
                // 回调的超时时间，单位为秒，默认为 3600
                'newJobProp[http_job.callback.timeout]'           => $params['callbackTimeout'] ?? "",
                // 用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
                'newJobProp[http_job.callback.code]'              => $params['callbackCode'] ?? "",
                // http_job类型的job特有，回调是否需要鉴token，如果需要传 1 ，默认不需要 0
                'newJobProp[http_job.callback.needToken]'         => $params['callbackNeedToken'] ?? "",
            ];

            $uri      = '/manager?ajax=setJobOverrideProperty';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = [];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            return $contents;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 查询project下的flow下的job列表信息
     * @param string $sessionId
     * @param string $project 项目名称
     * @param string $flowName flow名称
     * @return array
     */
    public function fetchFlowGraph(string $sessionId, string $project, string $flowName): array
    {
        $valid = true;
        $json  = [
            'session.id' => $sessionId ?? ($valid = null),
            'project'    => $project ?? ($valid = null),
            'flow'       => $flowName ?? ($valid = null),
        ];
        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/manager?ajax=fetchflowgraph';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['projectId', 'project', 'flow', 'nodes'];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            return $contents;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 查询project下flow下job的具体信息
     * @param string $sessionId
     * @param string $project 项目名称
     * @param string $flowName flow的名称
     * @param string $jobName job的名称
     * @return array
     */
    public function fetchJobInfo(string $sessionId, string $project, string $flowName, string $jobName): array
    {
        $valid = true;
        $json  = [
            'session.id' => $sessionId ?? ($valid = null),
            'project'    => $project ?? ($valid = null),
            'flowName'   => $flowName ?? ($valid = null),
            'jobName'    => $jobName ?? ($valid = null),
        ];
        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }
        try {
            $uri      = '/manager?ajax=fetchJobInfo';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['projectId', 'project', 'jobName', 'overrideParams', 'generalParams'];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            return $contents;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 查询flow历史执行任务job的详情
     * @param string $sessionId
     * @param string $execId
     * @return array|null
     */
    public function fetchExecFlow(string $sessionId, int $execId): array
    {
        $valid = true;
        $json  = [
            'session.id' => $sessionId ?? ($valid = null),
            // 执行任务的id，可以从上面的接口获取历史执行情况中得到
            'execid'     => $execId ?? ($valid = null),
        ];
        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }
        try {
            $uri      = '/executor?ajax=fetchexecflow';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = [
                'project', 'updateTime', 'type', 'attempt', 'execid', 'submitTime', 'nodes', 'nestedId', 'submitUser',
                'startTime', 'id', 'endTime', 'projectId', 'flowId', 'flow', 'status',
            ];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            return $contents;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 查询执行任务中job的具体日志
     * @param string $sessionId
     * @param int $execId
     * @param string $jobId
     * @param int $offset
     * @param int $length
     * @return array|null
     */
    public function fetchExecJobLogs(string $sessionId, int $execId, string $jobId, int $offset = 0, int $length = 100): array
    {
        $valid = true;
        $json  = [
            'session.id' => $sessionId ?? ($valid = null),
            // 执行任务的id，可以从上面的接口获取历史执行情况中得到
            'execid'     => $execId ?? ($valid = null),
            // 执行任务中job的名称
            'jobId'      => $jobId ?? ($valid = null),
            // 日志起始位置
            'offset'     => $offset,
            // 日志长度
            'length'     => $length,
        ];
        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }
        try {
            $uri      = '/executor?ajax=fetchexecflow';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = [
                'project', 'updateTime', 'type', 'attempt', 'execid', 'submitTime', 'nodes', 'nestedId', 'submitUser',
                'startTime', 'id', 'endTime', 'projectId', 'flowId', 'flow', 'status',
            ];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            return $contents;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc Fetch Executions of a Flow
     * 给定项目名称和某个流程后，该 API 调用会提供相应的执行列表。 这些执行按递交时间顺序排序。此外，还需要参数指定开始索引和列表长度。这原本是用来处理分页的。
     * @param string $sessionId
     * @param string $project
     * @param string $flow
     * @param int $start
     * @param int $length
     * @return array
     */
    public function fetchFlowExecutions(string $sessionId, string $project, string $flow, int $start = 0, int $length = 100): array
    {
        $valid = true;
        $json  = [
            //'ajax=fetchFlowExecutions'
            'ajax'       => 'fetchFlowExecutions',
            'session.id' => $sessionId ?? ($valid = null),
            // The project name to be fetched.
            'project'    => $project ?? ($valid = null),
            // The flow id to be fetched.
            'flow'       => $flow ?? ($valid = null),
            // The start index(inclusive) of the returned list.
            'start'      => $start,
            // The max length of the returned list. For example, if the start index is 2, and the length is 10, then the returned list will include executions of indices: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11].
            'length'     => $length,
        ];
        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/manager?';
            $response = $this->getGuzzleClient()->query($json)->get($uri);
            $fields   = [];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
                // 判断返回类型
                new ContentTypeResponseChain(),
                // 转换类型
                new ConvertArrayResponseChain(),
                // status=error
                new ContentStatusResponseChain(),
                // 含有 error 字段
                new ContentErrorResponseChain(),
                // 返回结构
                new ContentStructureResponseChain($fields),
            )->handle($response);

            return $contents;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

}
