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
use Teamone\DispatchClient\Gateway\ProjectGateway;

class ProjectGatewayImpl extends GatewayRequest implements ProjectGateway
{
    /**
     * @desc 新增 Project
     * @param string $sessionId
     * @param string $name 项目名称
     * @param string $description 项目描述
     * @return array
     */
    public function createProject(string $sessionId, string $name, string $description): array
    {
        $valid = true;

        $json = [
            'session.id'  => $sessionId ?? ($valid = null),
            'name'        => $name ?? ($valid = null),
            'description' => $description ?? ($valid = null),
        ];

        if (is_null($valid)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {

            $uri      = '/manager?action=create';
            $response = $this->getGuzzleClient()->formParams($json)->post($uri);

            // 响应返回的字段
            $fields   = ['path', 'action', 'status',];
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
                'path'   => $contents['path'],
                'action' => $contents['action'],
                'status' => $contents['status'],
            ];

            return $ret;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 初始化project
     * @param string $sessionId
     * @param string $project 要初始化的project的名称
     * @return array
     */
    public function initProject(string $sessionId, string $project, string $filepath): array
    {
        if (!file_exists($filepath)) {
            throw new InvalidArgumentException('Not Found File.');
        }

        $mime = mime_content_type($filepath);
        if ($mime !== 'application/zip') {
            throw new InvalidArgumentException('Not Support Mime Type, Please Input application/zip File.');
        }

        if (is_null($sessionId)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        if (is_null($project)) {
            throw new InvalidArgumentException('Arguments Error.');
        }

        try {

            $json = [
                [
                    'name'     => 'session.id',
                    'contents' => $sessionId,
                ],
                [
                    'name'     => 'project',
                    'contents' => $project,
                ],
                [
                    'name'     => 'ajax',
                    'contents' => 'upload',
                ],
                [
                    'name'     => 'file',
                    'contents' => fopen($filepath, 'rb'),
                    'filename' => basename($filepath),
                    'headers'  => [
                        'Content-Type' => 'application/zip'
                    ]
                ],
            ];

            $uri      = '/manager';
            $response = $this->getGuzzleClient()->multipart($json)->headers([])->post($uri);
            $fields   = ['projectId', 'version'];
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
                'version'   => $contents['version'],
            ];

            return $ret;
        } catch (RequestException $e) {
            throw new TeamoneDispatchClientException($e->getMessage(), 400, $e);
        }
    }

    /**
     * @desc 获取用户所有的项目
     * @param string $sessionId
     * @return array
     */
    public function fetchUserProjects(string $sessionId, string $user): array
    {
        try {
            $json = [
                'session.id' => $sessionId,
                'user'       => $user,
                'ajax'       => 'fetchuserprojects',
            ];

            $uri      = '/index';
            $response = $this->getGuzzleClient()->query($json)->get($uri);
            $fields   = ['projects'];
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
     * @desc Delete Project
     * @param string $sessionId
     * @param string $project
     * @return array
     */
    public function deleteProject(string $sessionId, string $project): array
    {
        try {
            $json = [
                'session.id' => $sessionId,
                'project'    => $project,
                'delete'     => true,
            ];

            $uri      = '/manager';
            $response = $this->getGuzzleClient()->query($json)->get($uri);
            $fields   = [];
            $contents = AbstractResponseChain::link(
            // 响应状态码
                new HttpStatusResponseChain([HttpStatusResponseChain::OK]),
//             判断返回类型
//                new ContentTypeResponseChain(),
                // 转换类型
//                new ConvertArrayResponseChain(),
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
