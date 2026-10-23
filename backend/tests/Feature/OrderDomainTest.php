<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_its_owner_and_stores_integer_prices(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $order = Order::factory()->for($owner)->create([
            'quantity' => 3,
            'unit_price' => 25_000,
            'total_price' => 75_000,
        ]);

        $this->assertTrue($order->user->is($owner));
        $this->assertTrue($owner->orders()->whereKey($order->id)->exists());
        $this->assertFalse($other->orders()->whereKey($order->id)->exists());
        $this->assertSame(3, $order->fresh()->quantity);
        $this->assertSame(25_000, $order->fresh()->unit_price);
        $this->assertSame(75_000, $order->fresh()->total_price);
    }

    public function test_deleting_a_user_cascades_to_their_orders(): void
    {
        $owner = User::factory()->create();
        $order = Order::factory()->for($owner)->create();

        $owner->delete();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }
}
