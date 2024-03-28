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
use Teamone\DispatchClient\Gateway\FlowGateway;
use Teamone\DispatchClient\Gateway\GatewayRequest;

class FlowGatewayImpl extends GatewayRequest implements FlowGateway
{
    /**
     * @desc 在project下添加flow
     * @param string $sessionId
     * @param string $project 项目名称
     * @return array
     */
    public function addFlowInCurrentProject(string $sessionId, string $project, string $flowName): array
    {
        $valid = true;

        $json = [
            'session.id'               => $sessionId ?? ($valid = null),
            'project'                  => $project ?? ($valid = null),
            'newFlowProp[newFlowName]' => $flowName ?? ($valid = null),
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {

            $uri      = '/manager?ajax=addFlowInCurrentProject';
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
     * @desc 在project下删除flow
     * @param string $sessionId
     * @param string $project 项目名称
     * @param string $flowName flow的名称
     * @return array
     */
    public function deleteFlowInCurrentProject(string $sessionId, string $project, string $flowName): array
    {
        $valid = true;
        $json  = [
            'session.id' => $sessionId ?? ($valid = null),
            'project'    => $project ?? ($valid = null),
            'flowName'   => $flowName ?? ($valid = null),
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {

            $uri      = '/manager?ajax=deleteFlowInCurrentProject';
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
     * @desc 查询当前project下的flow信息
     * @param string $sessionId
     * @param string $project 项目名称
     * @return array
     */
    public function fetchProjectFlows(string $sessionId, string $project): array
    {
        $valid = true;
        $json  = [
            'session.id' => $sessionId ?? ($valid = null),
            'project'    => $project ?? ($valid = null),
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/manager?ajax=fetchprojectflows';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['projectId', 'project', 'flows'];
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
                'flows'     => $contents['flows'],
            ];

            return $ret;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 查询flow正在执行的任务id
     * @param string $sessionId
     * @param string $project 项目名称
     * @param string $flowName 流程名称
     * @return array|null
     */
    public function getRunningExecutor(string $sessionId, string $project, string $flowName): array
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
            $uri      = '/executor?ajax=getRunning';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            $fields   = ['execIds'];
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
     * @desc 查询flow执行的历史情况
     * @param string $sessionId
     * @param string $project 项目名称
     * @param string $flow 流程名称
     * @param int $start 该接口为倒序查询，这个参数是起始位点，例如想从最近一次开始倒序查询，则传入 0
     * @param int $length 该接口为倒序查询，这个参数是倒数几个，最近2次的执行情况，start传入0的时候，该参数传入2
     * @return array
     */
    public function fetchFlowExecutions(string $sessionId, string $project, string $flow, int $start = 0, int $length = 20): array
    {
        $valid = true;

        $json = [
            'session.id' => $sessionId ?? ($valid = null),
            // 项目名称
            'project'    => $project ?? ($valid = null),
            // flow的名称
            'flow'       => $flow ?? ($valid = null),
            // 该接口为倒序查询，这个参数是起始位点，例如想从最近一次开始倒序查询，则传入 0
            'start'      => $start,
            // 该接口为倒序查询，这个参数是倒数几个，最近2次的执行情况，start传入0的时候，该参数传入2
            'length'     => $length,
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/manager?ajax=fetchFlowExecutions';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);
            // 响应返回的字段
            $fields   = ['total', 'executions', 'length', 'project', 'from', 'projectId', 'flow'];
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
     * @desc 查询flow的定时的配置
     * @param string $sessionId
     * @param string $projectId 项目名称
     * @param string $flowId 流程名称
     * @return array|null
     */
    public function fetchSchedule(string $sessionId, int $projectId, string $flowId): array
    {
        $valid = true;

        $json = [
            'session.id' => $sessionId ?? ($valid = null),
            // 项目id
            'projectId'  => $projectId ?? ($valid = null),
            // flow名称
            'flowId'     => $flowId ?? ($valid = null),
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {
            $uri      = '/schedule?ajax=fetchSchedule';
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
