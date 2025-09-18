<?php


namespace service\sign;

use \service\base\Base;
use service\base\Error;
/*use service\sign\models\Signdata;
use service\sign\interfaces\同searchSignData;*/
use \service\sign\models\Sign as ModSign;
use service\sign\models\SignDetail as Signdata;
use service\task\ExaminationTask;
use service\base\Module;
use service\sign\interfaces\Sign as SignInterface;

class Sign extends Module implements SignInterface
{
    public function __construct($id, $parent = null, $config = [])
    {
        parent::__construct([]);
    }

    /**
     * 添加签到计划
     * @param array $signs 签到信息组成的数组 ['name' => '今日签到']
     * @return array|bool
     */
    public function create(array $sign)
    {
        if (false === ($sign = ModSign::createItem($sign)))
            return $this->buildResponse('error', 400, 'failed to create sign');

        return $sign->getAttributes();
    }

    /**
     * @param array $sign_ids [1,2]
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getSignsById(array $sign_ids = [])
    {
        $signar = ModSign::find();

        is_array($sign_ids) && !empty($sign_ids) && $signar->where(['sign_id' => $sign_ids]);

        $signs = $signar->asArray()->all();

        return $signs;
    }

    public function getSignById($sign_id)
    {
        return ModSign::getItemById($sign_id);
    }

    /**
     * @param array $sign_ids [1,2]
     * @return bool
     */
    public function remove(array $sign_ids)
    {
        $where['sign_id'] = $sign_ids;

        return ModSign::deleteItems($where);
    }

    /**
     * @param $sign_id
     * @param array $sign_data
     */
    public function sign($sign_id, array $sign_data)
    {
        if (($sign = ModSign::findOne($sign_id)) == null)
            return $this->buildResponse('error', 400, 'sign was not found');

        $sign_data['sign_id'] = $sign_id;

        $sign = $sign->sign($sign_data);

        if (false === $sign) return $this->buildResponse('error', 400, 'failed to sign');

        return $sign->getAttributes();
    }


    /**
     *获取签到详情
     *
     * @param $sign_id
     * @param array $where
     * @return array|\service\base\type|\yii\db\ActiveRecord[]
     */
    public function getSignData($sign_id, array $where = [], $offset = 20, $limit = 20)
    {
        if (!is_array($sign_id)) $sign_id = [$sign_id];

        $where['sign_id'] = $sign_id;

        $details = Signdata::getItems($where, $offset, $limit);

        if (false === $details) return $this->buildResponse('error', 400, 'failed to get sign details data');

        return $details;
    }

    /**
     * @inheritDoc
     * @author yangzy 201218
     */
    public function searchSignData(array $map = [], $offset = -1, $limit = -1)
    {
        $data = Signdata::getItems($map, $offset, $limit);

        return $this->buildResponse('success', 201, $data);
    }

    /**
     * @inheritDoc
     * @author yangzy 201218
     */
    public function searchUserSignData($userId, array $map = [], $offset = -1, $limit = -1)
    {
        if (!is_array($userId)) $userId = [$userId];
        $map['unique_code'] = $userId;
        return $this->searchSignData($map, $offset, $limit);
    }

    /**
     * 计算用户签到次数
     *
     * @param string $userId 用户unique_code
     * @return array 【1】 返回用户签到次数
     * @design yangzy 20121218
     * @author yangzy 20121218
     */
    public function getUserSignCount($userId)
    {
        $data = $this->searchUserSignData($userId);

        if ($this->isErrorResponse($data))
            return $data;

        return $this->buildResponse('success', 201, count($data['data']));
    }


}

