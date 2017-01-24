<?php namespace App\Repositories\Contracts;

interface PaymentRepositoryInterface
{

    public function newCard(array $cardInfo, $userId);

    public function updateCard($id, array $cardInfo);

    public function deleteCard($id, $userId);

    public function get($id, $userId);

    public function all($userId);

    public function charge($id, $amount);

    public function voidTransaction($id);

    public function refund(); // !!REVISE!!
}
