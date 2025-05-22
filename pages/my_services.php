<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../templates/common.tpl.php';
require_once __DIR__ . '/../database/scripts/service.class.php';
require_once __DIR__ . '/../templates/service_table.tpl.php';
require_once __DIR__ . '/../database/scripts/joborder.class.php';
require_once __DIR__ . '/../templates/my_jobs.tpl.php';

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) {header('Location: /'); exit;}

$services = Service::getAllByUserId($user->id);

$allJobs     = JobOrder::getAllBySellerId($user->id);
$activeJobs  = array_filter($allJobs, function($o) { return !in_array($o->status, ['Completed', 'Cancelled'], true);});

drawHeader();
drawServiceTable($services, true);
if (!empty($activeJobs)) { drawMyJobsTable($activeJobs);}
drawFooter();
