<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use common\components\rbac\UserRoleRule;
class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); //удаляем старые данные
        //Создадим для примера права для доступа к админке
        $dashboard = $auth->createPermission('dashboard');
        $dashboard->description = 'Админ панель';
        $auth->add($dashboard);

        // права доступа
        $canManage = $auth->createPermission('can manage');
        $canManage->description = 'Менеджер';
        $auth->add($canManage);

        // права доступа клиента
        $client = $auth->createPermission('client');
        $client->description = 'Клиент';
        $auth->add($client);


        //Включаем наш обработчик
        $rule = new UserRoleRule();
        $auth->add($rule);
        //Добавляем роли
        $user = $auth->createRole('user');
        $user->description = 'Пользователь';
        $user->ruleName = $rule->name;
        $auth->add($user);
        $auth->addChild($user,$client);

        $manager = $auth->createRole('manager');
        $manager->description = 'Менеджер';
        $manager->ruleName = $rule->name;
        $auth->add($manager);

        //Добавляем потомков
        $auth->addChild($manager, $canManage);

        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';
        $admin->ruleName = $rule->name;
        $auth->add($admin);
        $auth->addChild($admin, $dashboard);
        $auth->addChild($admin, $manager);
    }
}