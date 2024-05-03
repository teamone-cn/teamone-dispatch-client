# WebMan API 

````php
<?php

namespace app\Controller\Http\Backend;

use app\Core\Factory\ContainerFactory;
use app\Exceptions\ValidateException;
use support\Redis;
use support\Request;
use support\Response;
use Teamone\DispatchClient\Builder\GatewayBuilder;
use Teamone\DispatchClient\Builder\GuzzleClientBuilder;
use Teamone\DispatchClient\Builder\GuzzleClientBuilderImpl;
use Teamone\DispatchClient\Gateway\AuthGateway;
use Teamone\DispatchClient\Gateway\ExecutorGateway;
use Teamone\DispatchClient\Gateway\FlowGateway;
use Teamone\DispatchClient\Gateway\JobGateway;
use Teamone\DispatchClient\Gateway\ProjectGateway;

class DispatchController
{
    /**
     * @var GuzzleClientBuilder
     */
    protected $guzzleClientBuilder;

    /**
     * @var AuthGateway
     */
    protected $authGateway;

    /**
     * @var ProjectGateway
     */
    protected $projectGateway;

    /**
     * @var FlowGateway
     */
    protected $flowGateway;

    /**
     * @var JobGateway
     */
    protected $jobGateway;

    /**
     * @var ExecutorGateway
     */
    protected $executorGateway;

    protected $config;
    protected $sessionId;


    public function __construct()
    {
        $config       = [
            'host'            => 'https://192.168.60.80:8443',
            'username'        => 'azkaban',
            'password'        => 'teamone',
            'connect_timeout' => 60,
            'timeout'         => 60,
            'debug'           => true,
            'verify'          => false,
        ];
        $this->config = $config;

        /** @var GuzzleClientBuilder guzzleClientBuilder */
        $this->guzzleClientBuilder = new GuzzleClientBuilderImpl($config);

        $this->authGateway = GatewayBuilder::authGatewayBuilder($this->guzzleClientBuilder);

        $sessionId = Redis::get('azkaban_session_id');
        if (empty($sessionId)) {
            $ret = $this->authGateway->login($config['username'], $config['password']);
            Redis::set('azkaban_session_id', $ret['session_id']);
            Redis::expire('azkaban_session_id', 3600);
            $sessionId = $ret['session_id'];
        }
        $this->sessionId = $sessionId;

        $this->projectGateway  = GatewayBuilder::projectGatewayBuilder($this->guzzleClientBuilder);
        $this->flowGateway     = GatewayBuilder::flowGatewayBuilder($this->guzzleClientBuilder);
        $this->jobGateway      = GatewayBuilder::jobGatewayBuilder($this->guzzleClientBuilder);
        $this->executorGateway = GatewayBuilder::executorGatewayBuilder($this->guzzleClientBuilder);

    }

    // projectGateway ***************************************

    // 查看项目
    public function fetchUserProjects(Request $request): Response
    {
        $ret = $this->projectGateway->fetchUserProjects($this->sessionId, $this->config['username']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 删除项目
    public function deleteProject(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }

        $ret = $this->projectGateway->deleteProject($this->sessionId, $params['project']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 创建项目
    public function createProject(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['name']) || empty($params['name'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['description']) || empty($params['description'])) {
            throw new ValidateException('Param description error.');
        }

        $filepath = $this->guzzleClientBuilder->getConfig()['init_file'] ?? '';
        if (empty($filepath)) {
            throw new ValidateException('init_file error.');
        }

        $ret = [];

        $ret['createProject'] = $this->projectGateway->createProject($this->sessionId, $params['name'], $params['description']);

        $ret['initProject'] = $this->projectGateway->initProject($this->sessionId, $params['name'], $filepath);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // flowGateway ***************************************

    // 在project下添加flow
    public function addFlowInCurrentProject(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }

        $ret = $this->flowGateway->addFlowInCurrentProject($this->sessionId, $params['project'], $params['flow']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 在project下删除flow
    public function deleteFlowInCurrentProject(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }

        $ret = $this->flowGateway->deleteFlowInCurrentProject($this->sessionId, $params['project'], $params['flow']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 查询当前project下的flow信息
    public function fetchProjectFlows(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }

        $ret = $this->flowGateway->fetchProjectFlows($this->sessionId, $params['project']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 查询flow正在执行的任务id
    public function getRunningExecutor(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }

        $ret = $this->flowGateway->getRunningExecutor($this->sessionId, $params['project'], $params['flow']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 查询flow执行的历史情况
    public function fetchFlowExecutions(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }

        $ret = $this->flowGateway->fetchFlowExecutions($this->sessionId, $params['project'], $params['flow']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 查询flow的定时的配置
    public function fetchSchedule(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['projectId']) || empty($params['projectId'])) {
            throw new ValidateException('Param projectId error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }

        $ret = $this->flowGateway->fetchSchedule($this->sessionId, $params['projectId'], $params['flow']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // jobGateway ***************************************

    // 查询project下的flow下的job列表信息
    public function fetchFlowGraph(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param projectId error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }

        $ret = $this->jobGateway->fetchFlowGraph($this->sessionId, $params['project'], $params['flow']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 查询project下flow下job的具体信息
    public function fetchJobInfo(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }
        if (!isset($params['job']) || empty($params['job'])) {
            throw new ValidateException('Param job error.');
        }

        $ret = $this->jobGateway->fetchJobInfo($this->sessionId, $params['project'], $params['flow'], $params['job']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 查询flow历史执行任务job的详情
    public function fetchExecFlow(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['execId']) || empty($params['execId'])) {
            throw new ValidateException('Param execId error.');
        }

        $ret = $this->jobGateway->fetchExecFlow($this->sessionId, $params['execId']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 查询执行任务中job的具体日志
    public function fetchExecJobLogs(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['execId']) || empty($params['execId'])) {
            throw new ValidateException('Param execId error.');
        }
        if (!isset($params['job']) || empty($params['job'])) {
            throw new ValidateException('Param execId error.');
        }

        $ret = $this->jobGateway->fetchExecJobLogs($this->sessionId, $params['execId'], $params['job']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 在project的flow下新增command job
    public function addJobInCurrentFlow(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }
        if (!isset($params['job']) || empty($params['job'])) {
            throw new ValidateException('Param job error.');
        }
        if (!isset($params['command']) || empty($params['command'])) {
            throw new ValidateException('Param command error.');
        }

        $dependOn = '';
        if (isset($params['dependOn']) || !empty($params['dependOn'])) {
            if (is_array($params['dependOn'])) {
                $dependOnTmp = array_filter($params['dependOn'], function ($item) {
                    return empty($item) ? false : true;
                });
                if (!empty($dependOnTmp)) {
                    $dependOn = implode(',', $params['dependOn']);
                }
            }
        }

        $ret = $this->jobGateway->addJobInCurrentFlow($this->sessionId, $params['project'], $params['flow'], $params['job'], $params['command'], $dependOn);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 在project的flow下删除多个job
    public function deleteJobsInCurrentFlow(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }
        if (!isset($params['job']) || empty($params['job'])) {
            throw new ValidateException('Param job error.');
        }

        $ret = $this->jobGateway->deleteJobsInCurrentFlow($this->sessionId, $params['project'], $params['flow'], $params['job']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 在project的flow下修改command job
    public function setJobOverrideProperty(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }
        if (!isset($params['job']) || empty($params['job'])) {
            throw new ValidateException('Param job error.');
        }
        if (!isset($params['command']) || empty($params['command'])) {
            throw new ValidateException('Param command error.');
        }
        dump($params);

        $ret = $this->jobGateway->setJobOverrideProperty($this->sessionId, $params['project'], $params['flow'], $params['job'], $params['command']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 在project的flow下新增http job
    public function addJobInCurrentFlowHttp(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }
        if (!isset($params['job']) || empty($params['job'])) {
            throw new ValidateException('Param job error.');
        }
        $dependOn = '';
        if (isset($params['dependOn']) || !empty($params['dependOn'])) {
            if (is_array($params['dependOn'])) {
                $dependOnTmp = array_filter($params['dependOn'], function ($item) {
                    return empty($item) ? false : true;
                });
                if (!empty($dependOnTmp)) {
                    $dependOn = implode(',', $params['dependOn']);
                }
            }
        }
        if (!isset($params['requestUrl']) || empty($params['requestUrl'])) {
            throw new ValidateException('Param requestUrl error.');
        }
        if (!isset($params['requestMethod']) || empty($params['requestMethod'])) {
            throw new ValidateException('Param requestMethod error.');
        }

        $json = [
            'sessionId'               => $this->sessionId,
            // 项目名称
            'project'                 => $params['project'],
            // flow的名称
            'flowName'                => $params['flow'],
            // 需要添加到flow下的job的名称
            'jobName'                 => $params['job'],
            // 需要添加到flow下的job的类型（http）
            'protocol'                => 'http',
            // 需要添加到flow下的job的依赖的job名称，多个以逗号隔开
            'dependOn'                => $dependOn,
            // http_job类型的job特有，请求的url
            'requestUrl'              => $params['requestUrl'],
            // http_job类型的job特有，请求的方式
            'requestMethod'           => $params['requestMethod'],
            // http_job类型的job特有，请求的参数类型
            'requestContentType'      => $params['requestContentType'] ?? 'application/json',
            // http_job类型的job特有，请求的参数（form风格或者json风格）
            'requestParam'            => $params['requestParam'] ?? '',
            // 请求的特有参数，用于标识请求得到的响应应该取哪部分数据作为参数传递给回调，默认为 “data”
            'requestCallbackParamKey' => $params['requestCallbackParamKey'] ?? 'data',
            // 请求的超时时间，单位为秒，默认为 3600
            'requestTimeout'          => $params['requestTimeout'] ?? '3600',
            // http_job类型的job特有，用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
            'requestCode'             => $params['requestCode'] ?? '999',
            // http_job类型的job特有，请求是否需要鉴token，如果需要传 1 ，默认不需要 0
            'requestNeedToken'        => $params['requestNeedToken'] ?? '0',
            // http_job类型的job特有，回调的url
            'callbackUrl'             => $params['callbackUrl'] ?? '',
            // http_job类型的job特有，回调的方式
            'callbackMethod'          => $params['callbackMethod'] ?? '',
            // http_job类型的job特有，回调的参数类型
            'contentType'             => $params['contentType'] ?? '',
            // http_job类型的job特有，回调的参数（form风格或者json风格）
            'callbackParam'           => $params['callbackParam'] ?? '',
            // 回调的超时时间，单位为秒，默认为 3600
            'callbackTimeout'         => $params['callbackTimeout'] ?? '3600',
            // 用于判断请求是否成功的自定义code，默认为 200，如果不需要验证code，那么传 999
            'callbackCode'            => $params['callbackCode'] ?? '999',
            // http_job类型的job特有，回调是否需要鉴token，如果需要传 1 ，默认不需要 0
            'callbackNeedToken'       => $params['callbackNeedToken'] ?? '0',
        ];

        $ret = $this->jobGateway->addJobInCurrentFlowHttp($json);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // jobGateway ***************************************
    // 在project下执行整个flow
    public function executeFlow(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }

        $ret = $this->executorGateway->executeFlow($this->sessionId, $params['project'], $params['flow']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 在project下停止整个flow
    public function cancelFlow(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['execId']) || empty($params['execId'])) {
            throw new ValidateException('Param execId error.');
        }

        $ret = $this->executorGateway->cancelFlow($this->sessionId, $params['execId']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 在project下定时执行flow
    public function scheduleCronFlow(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }
        if (!isset($params['cronExpression']) || empty($params['cronExpression'])) {
            throw new ValidateException('Param cronExpression error.');
        }
        if (isset($params['disabled']) && !is_array($params['disabled'])) {
            throw new ValidateException('Param disabled error.');
        }

        $params['disabled'] = $params['disabled'] ?? [];

        $ret = $this->executorGateway->scheduleCronFlow($this->sessionId, $params['project'], $params['flow'], $params['cronExpression'], $params['disabled']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 在project下执行flow下的指定jobs
    public function executeFlowJobs(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }
        if (!isset($params['job']) || empty($params['job'])) {
            throw new ValidateException('Param job error.');
        }

        $ret = $this->executorGateway->executeFlowJobs($this->sessionId, $params['project'], $params['flow'], $params['job']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 在project下停止执行flow下的指定jobs
    public function cancelFlowJobs(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['project']) || empty($params['project'])) {
            throw new ValidateException('Param project error.');
        }
        if (!isset($params['flow']) || empty($params['flow'])) {
            throw new ValidateException('Param flow error.');
        }
        if (!isset($params['job']) || empty($params['job'])) {
            throw new ValidateException('Param job error.');
        }
        if (!isset($params['execId']) || empty($params['execId'])) {
            throw new ValidateException('Param job error.');
        }

        $ret = $this->executorGateway->cancelFlowJobs($this->sessionId, $params['project'], $params['flow'], $params['job'], $params['execId']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 暂停执行某个任务
    public function pauseFlow(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['execId']) || empty($params['execId'])) {
            throw new ValidateException('Param job error.');
        }

        $ret = $this->executorGateway->pauseFlow($this->sessionId, $params['execId']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }

    // 重新调起暂停的任务
    public function resumeFlow(Request $request): Response
    {
        $params = $request->all();

        if (!isset($params['execId']) || empty($params['execId'])) {
            throw new ValidateException('Param execId error.');
        }

        $ret = $this->executorGateway->resumeFlow($this->sessionId, $params['execId']);

        return ContainerFactory::getResponseEntity()->success($ret);
    }
}
````
