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
use Teamone\DispatchClient\Gateway\ExecutorGateway;
use Teamone\DispatchClient\Gateway\GatewayRequest;

class ExecutorGatewayImpl extends GatewayRequest implements ExecutorGateway
{
    /**
     * @desc 在project下执行整个flow
     * @param string $sessionId
     * @param string $project 项目名称
     * @param string $flow 流程名称
     * @return array|null
     */
    public function executeFlow(string $sessionId, string $project, string $flow): array
    {
        $valid = true;

        $json = [
            'session.id' => $sessionId ?? ($valid = null),
            // 项目id
            'project'    => $project ?? ($valid = null),
            // flow的名称
            'flow'       => $flow ?? ($valid = null),
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/executor?ajax=executeFlow';
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
     * @desc 在project下停止整个flow
     * @param string $sessionId
     * @param int $execId
     * @return array|null
     */
    public function cancelFlow(string $sessionId, int $execId): array
    {

        $valid = true;

        $json = [
            'session.id' => $sessionId,
            // 项目id
            'execid'     => $execId,
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {

            $uri      = '/executor?ajax=cancelFlow';
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
     * @desc 在project下定时执行flow
     * @param string $sessionId
     * @param string $projectName
     * @param string $flow
     * @param string $cronExpression
     * @param array $disabled
     * @return array|null
     */
    public function scheduleCronFlow(string $sessionId, string $projectName, string $flow, string $cronExpression, array $disabled = []): array
    {
        $valid = true;

        $json = [
            'session.id'     => $sessionId ?? ($valid = null),
            // 项目id
            'projectName'    => $projectName ?? ($valid = null),
            // 定时执行的flow
            'flow'           => $flow ?? ($valid = null),
            // cron类型的定时表达式 例如 0 * * ? * * 为每分钟执行一次
            'cronExpression' => $cronExpression ?? ($valid = null),
            // 不需要定时执行的jobs数组，例如传入 ["jobA"]，那么jobA在定时执行时就不会执行
            'disabled'       => $disabled ?? [],
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/schedule?ajax=scheduleCronFlow';
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
     * @desc 在project下执行flow下的指定jobs
     * @param string $sessionId
     * @param string $project
     * @param string $flow
     * @param string $jobIds
     * @return array|null
     */
    public function executeFlowJobs(string $sessionId, string $project, string $flow, string $jobIds): array
    {
        $valid = true;

        $json = [
            'session.id' => $sessionId ?? ($valid = null),
            // 项目名称
            'project'    => $project ?? ($valid = null),
            // 定时执行的flow
            'flow'       => $flow ?? ($valid = null),
            // 需要指定执行的jobs，以逗号分隔传入
            'jobIds'     => $jobIds ?? ($valid = null),
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {

            $uri      = '/executor?ajax=executeFlowJobs';
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

    /** 在project下停止执行flow下的指定jobs
     * @desc
     * @param string $sessionId
     * @param string $project
     * @param string $flow
     * @param string $jobs
     * @param int $execId
     * @return array|null
     */
    public function cancelFlowJobs(string $sessionId, string $project, string $flow, string $jobs, int $execId): array
    {
        $valid = true;

        $json = [
            'session.id' => $sessionId ?? ($valid = null),
            // 项目名称
            'project'    => $project ?? ($valid = null),
            // 定时执行的flow
            'flow'       => $flow ?? ($valid = null),
            // 需要停止执行的jobs，以逗号分隔传入
            'jobs'       => $jobs ?? ($valid = null),
            // 需要停止执行的任务id
            'execid'     => $execId ?? ($valid = null),
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/executor?ajax=executeFlowJobs';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['execid', 'cancelFlow'];
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
     * @desc 暂停执行某个任务
     * @param string $sessionId
     * @param int $execId
     * @return array|null
     */
    public function pauseFlow(string $sessionId, int $execId): array
    {
        $valid = true;

        $json = [
            'session.id' => $sessionId,
            // 需要停止执行的任务id
            'execid'     => $execId,
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {

            $uri      = '/executor?ajax=pauseFlow';
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
     * @desc 重新调起暂停的任务
     * @param string $sessionId
     * @param int $execId
     * @return array|null
     */
    public function resumeFlow(string $sessionId, int $execId): array
    {
        $valid = true;

        $json = [
            'session.id' => $sessionId,
            // 需要停止执行的任务id
            'execid'     => $execId,
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/executor?ajax=resumeFlow';
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
}
