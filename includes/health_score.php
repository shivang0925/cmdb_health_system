<?php

function calculateHealthScore($data)
{
    $score = 0;

    if (!empty($data['location']))        $score += 10;
    if (!empty($data['ci_type']))        $score += 10;
    // if (!empty($data['owner']))          $score += 10;
    if (!empty($data['environment']))    $score += 10;
    if (!empty($data['status']))         $score += 10;
    if (!empty($data['priority']))       $score += 10;
    if (!empty($data['criticality']))    $score += 10;
    if (!empty($data['hostname']))       $score += 10;
    if (!empty($data['ip_address']))     $score += 10;
    if (!empty($data['support_group']))  $score += 10;
    if (!empty($data['version']))  $score += 10;
    
    // Apply a status-based cap so the score reflects the CI's actual
    // health, not just how many fields happen to be filled in.
    $status = $data['status'] ?? '';

    switch ($status) {
        case 'Failed':
            $score = min($score, 20); // A failed CI is always Critical
            break;
        case 'Maintenance':
            $score = min($score, 70); // Under maintenance -> capped at Warning
            break;
        case 'Retired':
            $score = min($score, 10); // Retired CIs shouldn't read as healthy
            break;
        // 'Active' (or anything else) -> no cap applied
    }

    return $score;
}
?>