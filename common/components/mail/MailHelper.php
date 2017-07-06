<?php
namespace common\components\mail;

use Yii;
use yii\base\Component;
class MailHelper extends Component
{
    public function test($email)
    {
        $mailer = \Yii::$app->mailer;
        $mailer->htmlLayout = 'layouts/test';
        return $mailer->compose('test/default', [])
            ->setFrom(\Yii::$app->params['noReplyEmail'])
            ->setTo($email)
            ->setSubject('Mail Service Test')
            ->send();
    }
}