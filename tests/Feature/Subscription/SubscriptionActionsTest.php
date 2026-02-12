<?php

describe('expire / reactivate actions', function () {
    it('admin can manually expire an active subscription', function () {})->todo();

    it('expire action is hidden for already expired subscriptions', function () {})->todo();

    it('regular user cannot see the expire action', function () {})->todo();

    it('admin can reactivate an expired subscription', function () {})->todo();

    it('reactivate action is hidden for active subscriptions', function () {})->todo();

    it('reactivate action is disabled when subscription is fully used up', function () {})->todo();

    it('regular user cannot see the reactivate action', function () {})->todo();
});

describe('bulk actions', function () {
    it('admin can bulk expire subscriptions', function () {})->todo();

    it('admin can bulk reactivate subscriptions', function () {})->todo();

    it('regular user cannot see bulk subscription actions', function () {})->todo();
});

describe('delete', function () {
    it('admin can delete a subscription', function () {})->todo();

    it('regular user cannot delete a subscription', function () {})->todo();

    it('admin can bulk delete subscriptions', function () {})->todo();
});
