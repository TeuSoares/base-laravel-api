<?php

use App\Core\Contracts\PaymentGateway;
use App\Core\Http\Middleware\Subscribed;
use App\Models\User;
use function Pest\Laravel\{actingAs, getJson, postJson};

function createBillingUser(array $overrides = []): User
{
    /** @var User $user */
    $user = User::factory()->create(array_merge(['language' => 'en'], $overrides));
    return $user;
}

// ─── Authentication ───────────────────────────────────────────────────────────

test('should require authentication to initiate checkout', function () {
    postJson(route('billing.checkout'))
        ->assertUnauthorized();
});

test('should require authentication to get subscription', function () {
    getJson(route('billing.subscription'))
        ->assertUnauthorized();
});

test('should require authentication to cancel subscription', function () {
    postJson(route('billing.cancel'))
        ->assertUnauthorized();
});

test('should require authentication to resume subscription', function () {
    postJson(route('billing.resume'))
        ->assertUnauthorized();
});

// ─── Subscription middleware ──────────────────────────────────────────────────

test('should require subscription to get subscription status', function () {
    $user = createBillingUser();

    actingAs($user)
        ->getJson(route('billing.subscription'))
        ->assertForbidden();
});

test('should require subscription to cancel', function () {
    $user = createBillingUser();

    actingAs($user)
        ->postJson(route('billing.cancel'))
        ->assertForbidden();
});

test('should require subscription to resume', function () {
    $user = createBillingUser();

    actingAs($user)
        ->postJson(route('billing.resume'))
        ->assertForbidden();
});

// ─── Checkout ─────────────────────────────────────────────────────────────────

test('should initiate checkout with default plan when no plan provided', function () {
    $user = createBillingUser();
    $checkoutUrl = 'https://checkout.stripe.com/test-session';

    $mock = Mockery::mock(PaymentGateway::class);
    $mock->shouldReceive('createCheckoutSession')->once()->andReturn($checkoutUrl);
    app()->instance(PaymentGateway::class, $mock);

    actingAs($user)
        ->postJson(route('billing.checkout'))
        ->assertOk()
        ->assertJsonPath('data.url', $checkoutUrl);
});

test('should initiate checkout with monthly plan', function () {
    $user = createBillingUser();
    $checkoutUrl = 'https://checkout.stripe.com/test-session';

    $mock = Mockery::mock(PaymentGateway::class);
    $mock->shouldReceive('createCheckoutSession')->once()->andReturn($checkoutUrl);
    app()->instance(PaymentGateway::class, $mock);

    actingAs($user)
        ->postJson(route('billing.checkout'), ['plan' => 'monthly'])
        ->assertOk()
        ->assertJsonPath('data.url', $checkoutUrl);
});

test('should initiate checkout with yearly plan', function () {
    $user = createBillingUser();
    $checkoutUrl = 'https://checkout.stripe.com/test-session';

    $mock = Mockery::mock(PaymentGateway::class);
    $mock->shouldReceive('createCheckoutSession')->once()->andReturn($checkoutUrl);
    app()->instance(PaymentGateway::class, $mock);

    actingAs($user)
        ->postJson(route('billing.checkout'), ['plan' => 'yearly'])
        ->assertOk()
        ->assertJsonPath('data.url', $checkoutUrl);
});

test('should fail checkout with invalid plan', function () {
    $user = createBillingUser();

    actingAs($user)
        ->postJson(route('billing.checkout'), ['plan' => 'invalid'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['plan']);
});

// ─── Subscription management ──────────────────────────────────────────────────

test('should return subscription data', function () {
    $user = createBillingUser();

    $subscriptionData = [
        'plan'      => 'price_monthly',
        'status'    => 'active',
        'active'    => true,
        'cancelled' => false,
    ];

    $mock = Mockery::mock(PaymentGateway::class);
    $mock->shouldReceive('getSubscription')->once()->andReturn($subscriptionData);
    app()->instance(PaymentGateway::class, $mock);

    actingAs($user)
        ->withoutMiddleware(Subscribed::class)
        ->getJson(route('billing.subscription'))
        ->assertOk()
        ->assertJsonPath('data.status', 'active')
        ->assertJsonPath('data.active', true);
});

test('should cancel subscription', function () {
    $user = createBillingUser();

    $mock = Mockery::mock(PaymentGateway::class);
    $mock->shouldReceive('cancelSubscription')->once();
    app()->instance(PaymentGateway::class, $mock);

    actingAs($user)
        ->withoutMiddleware(Subscribed::class)
        ->postJson(route('billing.cancel'))
        ->assertOk()
        ->assertJsonPath('message', __('billing.cancelled'));
});

test('should resume subscription', function () {
    $user = createBillingUser();

    $mock = Mockery::mock(PaymentGateway::class);
    $mock->shouldReceive('resumeSubscription')->once();
    app()->instance(PaymentGateway::class, $mock);

    actingAs($user)
        ->withoutMiddleware(Subscribed::class)
        ->postJson(route('billing.resume'))
        ->assertOk()
        ->assertJsonPath('message', __('billing.resumed'));
});
