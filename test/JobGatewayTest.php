<?php

namespace Teamone\DispatchClientTest;

class JobGatewayTest extends AuthGatewayTest
{
    public function testFetchFlowGraph()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow";
        $ret     = $this->jobGateway->fetchFlowGraph($this->sessionId, $project, $flow);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testFetchJobInfo()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow";
        $job     = "test-project-0326-1354-flow-job";
        $ret     = $this->jobGateway->fetchJobInfo($this->sessionId, $project, $flow, $job);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testFetchExecFlow()
    {
        $execId = 131;
        $ret    = $this->jobGateway->fetchExecFlow($this->sessionId, $execId);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testFetchExecJobLogs()
    {
        $job    = "test-project-0326-1354-flow-job";
        $execId = 134;
        $ret    = $this->jobGateway->fetchExecJobLogs($this->sessionId, $execId, $job, 0, 1200);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testAddJobInCurrentFlow()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow-v2";
        $job     = "test-project-0326-1354-flow-v2-job-v3";
        $command = "sleep 3";

        $ret = $this->jobGateway->addJobInCurrentFlow($this->sessionId, $project, $flow, $job, $command);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testDeleteJobsInCurrentFlow()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow";
        $job     = "svipapi_request_token";

        $ret = $this->jobGateway->deleteJobsInCurrentFlow($this->sessionId, $project, $flow, $job);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testSetJobOverrideProperty()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow-v2";
        $job     = "test-project-0326-1354-flow-v2-job-v2";
        $command = "sleep 501";

        $ret = $this->jobGateway->setJobOverrideProperty($this->sessionId, $project, $flow, $job, $command);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testAddJobInCurrentFlowHttp()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow";
        $job     = "svipapi_request_token";

        $json = [
            'sessionId'               => $this->sessionId,
            // 项目名称
            'project'                 => $project,
            // flow的名称
            'flowName'                => $flow,
            // 需要添加到flow下的job的名称
            'jobName'                 => $job,
            // 需要添加到flow下的job的类型（http）
            'protocol'                => 'http',
            // 需要添加到flow下的job的依赖的job名称，多个以逗号隔开
            'dependOn'                => "",
            // http_job类型的job特有，请求的url
            'requestUrl'              => "https://dev-svip-api.thwpmanage.com/api/vip/user/login",
            // http_job类型的job特有，请求的方式
            'requestMethod'           => "post",
            // http_job类型的job特有，请求的参数类型
            'requestContentType'      => "application/json",
            // http_job类型的job特有，请求的参数（form风格或者json风格）
            'requestParam'            => [
                "email"    => "neil@tranhom.com",
                "password" => "123456",
            ],
            // 请求的特有参数，用于标识请求得到的响应应该取哪部分数据作为参数传递给回调，默认为 “data”
            'requestCallbackParamKey' => "data",
            // 请求的超时时间，单位为秒，默认为 3600
            'requestTimeout'          => "3600",
            // http_job类型的job特有，用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
            'requestCode'             => "999",
            // http_job类型的job特有，请求是否需要鉴token，如果需要传 1 ，默认不需要 0
            'requestNeedToken'        => "0",
            // http_job类型的job特有，回调的url
            'callbackUrl'             => "",
            // http_job类型的job特有，回调的方式
            'callbackMethod'          => "",
            // http_job类型的job特有，回调的参数类型
            'contentType'             => "",
            // http_job类型的job特有，回调的参数（form风格或者json风格）
            'callbackParam'           => "",
            // 回调的超时时间，单位为秒，默认为 3600
            'callbackTimeout'         => "3600",
            // 用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
            'callbackCode'            => "999",
            // http_job类型的job特有，回调是否需要鉴token，如果需要传 1 ，默认不需要 0
            'callbackNeedToken'       => "0",
        ];

        $ret = $this->jobGateway->addJobInCurrentFlowHttp($json);
        dump($ret);
        $this->assertTrue(true);
    }

    public function testAddJobInCurrentFlowHttpCallback()
    {
        $project = "test-project-0326-1354";
        $flow    = "test-project-0326-1354-flow";
        $job     = "svipapi_get_sign";

        $json = [
            'sessionId'               => $this->sessionId,
            // 项目名称
            'project'                 => $project,
            // flow的名称
            'flowName'                => $flow,
            // 需要添加到flow下的job的名称
            'jobName'                 => $job,
            // 需要添加到flow下的job的类型（http）
            'protocol'                => 'http',
            // 需要添加到flow下的job的依赖的job名称，多个以逗号隔开
            'dependOn'                => "svipapi_request_token",
            // http_job类型的job特有，请求的url
            'requestUrl'              => "https://dev-svip-api.thwpmanage.com/api/vip/test/getSign",
            // http_job类型的job特有，请求的方式
            'requestMethod'           => "post",
            // http_job类型的job特有，请求的参数类型，如 application/x-www-form-urlencoded 或 application/json
            'requestContentType'      => "application/x-www-form-urlencoded",
            // http_job类型的job特有，请求的参数（form风格或者json风格）
            'requestParam'            => "",
            // 请求的特有参数，用于标识请求得到的响应应该取哪部分数据作为参数传递给回调，默认为 “data”
            'requestCallbackParamKey' => "",
            // 请求的超时时间，单位为秒，默认为 3600
            'requestTimeout'          => "",
            // http_job类型的job特有，用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
            'requestCode'             => "",
            // http_job类型的job特有，请求是否需要鉴token，如果需要传 1 ，默认不需要 0
            'requestNeedToken'        => "1",

            // http_job类型的job特有，回调的url
            'callbackUrl'             => "",
            // http_job类型的job特有，回调的方式
            'callbackMethod'          => "",
            // http_job类型的job特有，回调的参数类型，如 application/x-www-form-urlencoded 或 application/json
            'contentType'             => "",
            // http_job类型的job特有，回调的参数（form风格或者json风格）
            "callbackParam"           => "",
            // 回调的超时时间，单位为秒，默认为 3600
            'callbackTimeout'         => "",
            // 用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
            'callbackCode'            => "",
            // http_job类型的job特有，回调是否需要鉴token，如果需要传 1 ，默认不需要 0
            'callbackNeedToken'       => "",
        ];

        $ret = $this->jobGateway->addJobInCurrentFlowHttp($json);
        dump($ret);
        $this->assertTrue(true);
    }
}
