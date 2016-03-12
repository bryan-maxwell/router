<?php

require_once __DIR__ . '/../src/Router/AbstractRouter.php';
require_once __DIR__ . '/../src/Router/Router.php';
@include __DIR__ . '/../../lib/vendor/autoload.php';
@include __DIR__ . '/../../../autoload.php';

Tracy\Debugger::enable(FALSE);

$r = new Lemmon\Router\Router;

/*
$r->match(['GET', 'PUT'], '', function() {
    dump('== empty ==');
});
$r->match('{controller}(/(?<trail>{action:read|write|update}(/{id:num:1,3=1}))!)', ['controller' => '\w+'], function($r) {
    dump('controller/action/id');
}, 'Default');
$r->match(['POST'], function() {
    dump('== POST ==');
});
$r->match('*', function() {
    dump('== * ==');
});
$r->match(function() {
    dump('== default ==');
});
*/
#$r->match('{action:signin|signup}', 'Auth#');
#$r->match('logout', 'Auth#signout');
#$r->match('{controller:page}(/{page:num:1,5=1})', 'Posts#');
#$r->match('({controller=index}(/{action=index}(/{id:num=1})))', 'crud');
$r->match('({link=index})', function(){}, 'link');
$r->dispatch();

echo '<ul>';
echo '<li><a href=' .($_ = $r->to(':link', ['link' => 'index'])). '>' .$_. '</a></li>';
echo '<li><a href=' .($_ = $r->to(':link', ['link' => 'subpage'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':crud', ['controller' => 'foo', 'action' => 'bar'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':crud', ['controller' => 'index'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':crud', ['controller' => 'xoxo', 'action' => 'index'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':crud', ['controller' => 'index', 'action' => 'index'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':crud', ['controller' => 'index', 'action' => 'index', 'id' => 1])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':crud', ['controller' => 'index', 'action' => 'index', 'id' => 2])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':crud', ['controller' => 'index', 'action' => 'action'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Auth#')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Auth#', ['action' => 'signin'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Auth#', ['action' => 'signup'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Auth#signout')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->getRoot()). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to('controller')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to('controller/read')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to('controller/read/13')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to('controller/read/xoxo')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to('controller/read/1')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to('signin')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to('signup')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to('logout')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Auth#signout')). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Posts#page', ['page' => 1])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Posts#page', ['page' => 2])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Posts#page', ['page' => 3])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to('controller/{action}/{id}', ['action' => 'update', 'id' => 33])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['action' => 'update', 'id' => 0])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['action' => 'update', 'id' => 1])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['action' => 'update', 'id' => 2])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['action' => 'update', 'id' => 33])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['action' => 'update', 'id' => FALSE])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['action' => 'update', 'id' => NULL])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['action' => 'xo'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['controller' => 'xo'])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['id' => 11])). '>' .$_. '</a></li>';
#echo '<li><a href=' .($_ = $r->to(':Default', ['id' => 1])). '>' .$_. '</a></li>';
echo '</ul>';

/**/
dump($r);
dump([
    $r->controller,
    $r->action,
    $r->id,
    $r->page,
    $r->link,
    $r->getName(),
]);
/**/

die('n/a');
