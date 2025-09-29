<?php

namespace App\Service;

use App\Entity\Course;

class CourseService {
    public function generateAccessCode(Course $course): void
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < 10; $i++) {
            $randomIndex = random_int(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }

        $course->setAccessCode($randomString);
    }

}

