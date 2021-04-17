<?php

namespace App\Service;

use App\Entity\Agreement;
use Doctrine\ORM\EntityManagerInterface;

class AgreementService {

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
	
	public function findCurrentAgreements($in_registration_form = true, $only_is_required = false) {
        if ($in_registration_form && $only_is_required)
            $agreement = $this->entityManager->getRepository(Agreement::class)->findBy(['in_registration_form' => 1, 'is_required' => 1], ['id' => 'DESC']);
        else if ($in_registration_form)
            $agreement = $this->entityManager->getRepository(Agreement::class)->findBy(['in_registration_form' => 1], ['id' => 'DESC']);
        else if ($only_is_required)
            $agreement = $this->entityManager->getRepository(Agreement::class)->findBy(['is_required' => 1], ['id' => 'DESC']);
        else
            $agreement = $this->entityManager->getRepository(Agreement::class)->findAll(['id' => 'DESC']);
        
        $checked = array();

        foreach ($agreement as $a) {
            $flag = false;

            for ($i=0; $i<count($checked); $i++) {
                if ($a->getSignature() == $checked[$i]->getSignature()) {
                    $flag = true;
                    break;
                }
            }

            if (!$flag && $a->getDateOfEntry() <= new \DateTime())
                $checked[] = $a;
        }

        return $checked;
 	}
}