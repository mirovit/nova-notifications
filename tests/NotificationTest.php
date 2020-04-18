<?php

use Mirovit\NovaNotifications\Notification;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    /**
    * @test
    */
    public function it_creates_same_data_structure_with_static_call()
    {
        $newInstance = new Notification();
        $staticInstance = Notification::make();


        $this->assertInstanceOf(Notification::class, $newInstance);
        $this->assertInstanceOf(Notification::class, $staticInstance);

        $this->assertSame($newInstance->toArray(), $staticInstance->toArray());
    }

    /**
    * @test
    */
    public function it_has_default_values_on_instantiation()
    {
        $notification = Notification::make();

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(3, $notification);
        $this->assertArrayHasKey('created_at', $notification);
        $this->assertArrayHasKey('show_mark_as_read', $notification);
        $this->assertArrayHasKey('show_cancel', $notification);
        $this->assertTrue($notification['show_mark_as_read']);
        $this->assertTrue($notification['show_cancel']);
    }

    /**
    * @test
    */
    public function it_sets_title_when_new_static_instance_created()
    {
        $title = 'Title';

        $notification = Notification::make($title);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);
        $this->assertArrayHasKey('title', $notification);
        $this->assertSame($title, $notification['title']);
    }

    /**
    * @test
    */
    public function it_sets_subtitle_when_new_static_instance_created()
    {
        $subtitle = 'Sub Title';

        $notification = Notification::make(null, $subtitle);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);
        $this->assertArrayHasKey('subtitle', $notification);
        $this->assertSame($subtitle, $notification['subtitle']);
    }

    /**
    * @test
    */
    public function it_sets_title()
    {
        $title = 'Test Title';

        $notification = Notification::make()->title($title);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);
        $this->assertArrayHasKey('title', $notification);
        $this->assertSame($title, $notification['title']);
    }

    /**
    * @test
    */
    public function it_sets_subtitle()
    {
        $subtitle = 'Sub title';

        $notification = Notification::make()->subtitle($subtitle);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);
        $this->assertArrayHasKey('subtitle', $notification);
        $this->assertSame($subtitle, $notification['subtitle']);
    }

    /**
    * @test
    */
    public function it_sets_internal_link()
    {
        $link = 'https://website.com';

        $notification = Notification::make()->link($link);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(5, $notification);
        $this->assertArrayHasKey('url', $notification);
        $this->assertArrayHasKey('external', $notification);
        $this->assertSame($link, $notification['url']);
        $this->assertFalse($notification['external']);
    }

    /**
    * @test
    */
    public function it_sets_external_link()
    {
        $link = 'https://website.com';

        $notification = Notification::make()->link($link, true);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(5, $notification);
        $this->assertArrayHasKey('url', $notification);
        $this->assertArrayHasKey('external', $notification);
        $this->assertSame($link, $notification['url']);
        $this->assertTrue($notification['external']);
    }

    /**
    * @test
    */
    public function it_sets_route_without_identifier()
    {
        $name = 'name';
        $resourceName = 'resource-name';

        $notification = Notification::make()->route($name, $resourceName);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);
        $this->assertArrayHasKey('route', $notification);
        $this->assertCount(2, $notification['route']);
        $this->assertCount(1, $notification['route']['params']);
        $this->assertSame($name, $notification['route']['name']);
        $this->assertSame($resourceName, $notification['route']['params']['resourceName']);
    }

    /**
    * @test
    */
    public function it_sets_route_with_identifier()
    {
        $name = 'name';
        $resourceName = 'resource-name';
        $resourceId = 'resource-id';

        $notification = Notification::make()->route($name, $resourceName, $resourceId);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);
        $this->assertArrayHasKey('route', $notification);
        $this->assertCount(2, $notification['route']);
        $this->assertCount(2, $notification['route']['params']);
        $this->assertSame($name, $notification['route']['name']);
        $this->assertSame($resourceName, $notification['route']['params']['resourceName']);
        $this->assertSame($resourceId, $notification['route']['params']['resourceId']);
    }

    /**
     * @test
     */
    public function it_sets_route_to_index()
    {
        $resourceName = 'resource-name';

        $notification = Notification::make()->routeIndex($resourceName);

        $this->routeWithoutIdetifier($notification, 'index', 'resource-name');
    }

    /**
     * @test
     */
    public function it_sets_route_to_create()
    {
        $resourceName = 'resource-name';

        $notification = Notification::make()->routeCreate($resourceName);

        $this->routeWithoutIdetifier($notification, 'create', $resourceName);
    }

    /**
    * @test
    */
    public function it_sets_route_to_edit()
    {
        $resourceName = 'resource-name';
        $resourceId = 1;

        $notification = Notification::make()->routeEdit($resourceName, $resourceId);

        $this->routeWithIdetifier($notification, 'edit', $resourceName, $resourceId);
    }

    /**
    * @test
    */
    public function it_sets_route_to_detail()
    {
        $resourceName = 'resource-name';
        $resourceId = 1;

        $notification = Notification::make()->routeDetail($resourceName, $resourceId);

        $this->routeWithIdetifier($notification, 'detail', $resourceName, $resourceId);
    }

    /**
    * @test
    */
    public function it_sets_level()
    {
        $level = 'info';

        $notification = Notification::make()->level($level);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);
        $this->assertArrayHasKey('level', $notification);
        $this->assertSame($level, $notification['level']);
    }

    /**
    * @test
    */
    public function it_defaults_level_to_info_when_wrong_value_is_passed()
    {
        $level = 'wrong-level';

        $notification = Notification::make()
            ->level($level);


        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);
        $this->assertArrayHasKey('level', $notification);
        $this->assertNotSame($level, $notification['level']);
        $this->assertSame('info', $notification['level']);
    }

    /**
    * @test
    */
    public function it_sets_title_and_info_level()
    {
        $title = 'Title';

        $notification = Notification::make()->info($title);

        $this->titleAndLevel($notification, $title, 'info');
    }

    /**
    * @test
    */
    public function it_sets_title_and_success_level()
    {
        $title = 'Title';

        $notification = Notification::make()->success($title);

        $this->titleAndLevel($notification, $title, 'success');
    }

    /**
    * @test
    */
    public function it_sets_title_and_error_level()
    {
        $title = 'Title';

        $notification = Notification::make()->error($title);

        $this->titleAndLevel($notification, $title, 'error');
    }

    /**
    * @test
    */
    public function it_sets_created_at()
    {
        $createdAt = \Carbon\Carbon::now()->addDay();

        $notification = Notification::make();
        $this->assertNotSame($createdAt->toAtomString(), $notification->toArray()['created_at']);

        $notification = $notification->createdAt($createdAt);

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertSame($createdAt->toAtomString(), $notification->toArray()['created_at']);
    }

    /**
    * @test
    */
    public function it_sets_icon()
    {
        $icon = 'some icon-class';

        $notification = Notification::make()->icon($icon);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);
        $this->assertArrayHasKey('icon', $notification);
        $this->assertSame($icon, $notification['icon']);
    }

    /**
    * @test
    */
    public function it_returns_an_array()
    {
        $notification = Notification::make()->toArray();

        $this->assertTrue(is_array($notification));
        $this->assertCount(3, $notification);
    }

    /**
    * @test
    */
    public function it_has_correct_level_constants()
    {
        $this->assertCount(3, Notification::LEVELS);
        $this->assertSame(['info', 'success', 'error'], Notification::LEVELS);
    }

    /**
     * @test
     */
    public function it_hides_show_mark_as_read()
    {
        $notification = Notification::make()->showMarkAsRead(false);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(3, $notification);
        $this->assertFalse($notification['show_mark_as_read']);
    }

    /**
     * @test
     */
    public function it_hides_cancel()
    {
        $notification = Notification::make()->showCancel(false);

        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(3, $notification);
        $this->assertFalse($notification['show_cancel']);
    }

    protected function routeWithoutIdetifier($notification, $name, $resourceName)
    {
        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);

        $this->assertArrayHasKey('route', $notification);

        $this->assertCount(2, $notification['route']);
        $this->assertCount(1, $notification['route']['params']);

        $this->assertSame($name, $notification['route']['name']);
        $this->assertSame($resourceName, $notification['route']['params']['resourceName']);
    }

    protected function routeWithIdetifier($notification, $name, $resourceName, $resourceId)
    {
        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(4, $notification);

        $this->assertArrayHasKey('route', $notification);

        $this->assertCount(2, $notification['route']);
        $this->assertCount(2, $notification['route']['params']);

        $this->assertSame($name, $notification['route']['name']);
        $this->assertSame($resourceName, $notification['route']['params']['resourceName']);
        $this->assertSame($resourceId, $notification['route']['params']['resourceId']);
    }

    protected function titleAndLevel($notification, $title, $level)
    {
        $this->assertInstanceOf(Notification::class, $notification);

        $notification = $notification->toArray();

        $this->assertCount(5, $notification);

        $this->assertArrayHasKey('title', $notification);
        $this->assertArrayHasKey('level', $notification);

        $this->assertSame($title, $notification['title']);
        $this->assertSame($level, $notification['level']);
    }
}