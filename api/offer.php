<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/scripts/offer.class.php';
require_once __DIR__ . '/../database/scripts/joborder.class.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {echo json_encode(['status'=>'error','message'=>'Not logged in']); exit;}
$userId = $session->getUser()->id;
$input  = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

try {
  switch($action) {
    case 'create':
      Offer::create(
        $userId,
        (int)$input['buyerId'],
        (int)$input['serviceId'],
        $input['requirements'] ?? '',
        (float)$input['price'],
        $input['currency']
      );
      echo json_encode(['status'=>'success']);
      break;

    case 'accept':
    case 'decline':
      $offerId = (int)($input['offerId'] ?? 0);
      $newStatus = ($action==='accept') ? 'accepted' : 'declined';
      Offer::respond($offerId, $newStatus);

      if ($newStatus === 'accepted') {
        $offer = Offer::getById($offerId);
        JobOrder::createFromOffer($offer);
      }
    case 'list':
      $other = (int)($_GET['with'] ?? 0);
      $offers = Offer::getBetween($userId, $other);
      $out = [];
      foreach ($offers as $o) {
        $out[] = [
          'type'         => 'offer',
          'offerId'      => $o->id,
          'senderId'     => $o->sellerId,
          'receiverId'   => $o->buyerId,
          'serviceId'    => $o->serviceId,
          'requirements' => $o->requirements,
          'price'        => $o->price,
          'currency'     => $o->currency,
          'status'       => $o->status,
          'timestamp'    => $o->createdAt
        ];
      }
      echo json_encode(['status'=>'success','offers'=>$out]);
      break;

    default:
      echo json_encode(['status'=>'error','message'=>'Unknown action']);
  }
} catch (PDOException $e) {
  echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
