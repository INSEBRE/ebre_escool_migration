<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Scool\EbreEscoolModel\Teacher;

/**
 * Class EbreEscoolDatabaseTest
 */
class EbreEscoolDatabaseTest extends TestCase
{
    /**
     * Test teachers
     *
     * @return void
     */
    public function testTeachers()
    {
        $allTeachers = Teacher::all();
        $totalTeachers = $allTeachers->count();
        $this->assertTrue($totalTeachers>99,'There are less than 100 teachers!');

        foreach ($allTeachers as $teacher) {
            $this->assertTrue($teacher->name != "",
                'Teacher ' . $teacher->name . ' has no name! ' . '( teacher id: ' .
                $teacher->id . ')');
            $this->assertTrue(!filter_var($teacher->email, FILTER_VALIDATE_EMAIL ===false ),
                'Teacher ' . $teacher->name . ' has no valid email : ' . $teacher->email .' ! ' . '( teacher id: ' .
                $teacher->id . ')');
            $this->assertTrue($teacher->person != null,
                'Teacher ' . $teacher->name . ' has no personal data! ' . '( teacher id: ' .
                $teacher->id . ')');
            $this->assertTrue($teacher->user != null,
                'Teacher ' . $teacher->name . ' has no user data! ' . '( teacher id: ' .
                $teacher->id . ')');
            $this->assertTrue($teacher->user->person_id == $teacher->person->id,
                'Teacher ' . $teacher->name . ' has incoherent data! ' . '( teacher id: ' .
                $teacher->id . ')');
        }

        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
            $currentTeachers = Teacher::activeOn($academicPeriod->id)->get();
            if ($academicPeriod->id != 6 && $academicPeriod->id != 7) continue;
            $this->assertTrue($totalTeachers > $currentTeachers->count(),'There are more current teachers than active teachers!');
            foreach ($currentTeachers as $currentTeacher) {
                $this->assertTrue(
                    is_numeric($code = $currentTeacher->details()->activeOn($academicPeriod->id)->first()->code)
                    || ends_with($code,'S'),
                    "Teacher code format is incorrect");
                try {
                    $department = \Scool\EbreEscoolModel\Department::findOrFail(
                        $department_id = $currentTeacher->details()->activeOn($academicPeriod->id)->first()->department_id);
                    $this->assertTrue(true);
                } catch (\Exception $e) {
                    $this->assertTrue(false, 'Teacher ' . $teacher->name . ' has incorrect department id: ' .
                        $department_id . '( teacher id: ' . $teacher->id . ')');
                }

            }
        }
    }

    /**
     * Test departments.
     *
     * @return void
     */
    public function testDepartments()
    {
        $departments = \Scool\EbreEscoolModel\Department::all();
        $this->assertTrue($departments->count()>4,'There are less than 5 departments!');

        foreach ($departments as $department) {
            $this->assertTrue($department->name != "",
                'Department ' . $department->name . ' has no name! ' . '( department id: ' .
                $department->id . ')');
            $this->assertTrue($department->shortname != "",
                'Department ' . $department->name . ' has no shortname! ' . '( department id: ' .
                $department->id . ')');
            try {
                $head = Teacher::findOrFail($department->head);
                $this->assertTrue(true);
                $this->assertTrue($head->active,'Department ' . $department->name . ' has not active head id: ' .
                    $department->head . '( department id: ' . $department->id . ')');
            } catch (\Exception $e) {
                $this->assertTrue(false, 'Department ' . $department->name . ' has incorrect head id: ' .
                    $department->head . '( department id: ' . $department->id . ')');
            }

            try {
                \Scool\EbreEscoolModel\Location::findOrFail($department->location_id);
                $this->assertTrue(true);
            } catch (\Exception $e) {
                $this->assertTrue(false, 'Department ' . $department->name . ' has incorrect location id: ' .
                    $department->location_id . '( department id: ' . $department->id . ')');
            }

            $this->assertTrue($department->allStudies()->count() > 0, 'Department ' . $department->name . ' does not have any studies!');

            $this->assertTrue($department->studies()->count() > 0, 'Department ' . $department->name . ' does not have any active studies!');
        }

        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
            foreach ($departments as $department) {
                $this->assertTrue($department->studiesActiveOn($academicPeriod->id)->count() > 0, 'Department ' .
                    $department->name . ' does not have any active study for period:' . $academicPeriod->id .  '!');
            }
        }
    }

    /**
     * Test studies.
     * @group failing
     * @return void
     */
    public function testStudies()
    {
        $studies = \Scool\EbreEscoolModel\Study::all();
        $totalStudies = $studies->count();
        $this->assertTrue($totalStudies > 19,'There are less than 20 studies!');
        foreach ($studies as $study) {
            $this->assertTrue($study->name != "",
                'Study ' . $study->name . ' has no name! ' . '( study id: ' .
                $study->id . ')');
            $this->assertTrue($study->shortname != "",
                'Study ' . $study->name . ' has no shortname! ' . '( study id: ' .
                $study->id . ')');
            $this->assertTrue($study->allCourses()->count() > 0, 'Study ' . $study->name . '( ' . $study->id. ' )' . ' does not have any courses!');
            $this->assertTrue($study->allCourses()->count() < 4 , 'Study ' . $study->name . '( ' . $study->id. ' )' . ' have more than 3 courses!');
        }

        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
            $activeStudies = \Scool\EbreEscoolModel\Study::activeOn($academicPeriod->id);
            foreach ($activeStudies->get() as $activeStudy) {
//                $this->assertTrue($activeStudy->courses()->count() > 0, 'Study ' . $activeStudy->name
//                    . '( ' . $activeStudy->id . ' )' . ' does not have any active courses for period: '
//                    . $academicPeriod->id . ' !');
//                $this->assertTrue($activeStudy->courses()->count() < 4, 'Study ' . $activeStudy->name .
//                    '( ' . $activeStudy->id . ' )' . ' have more than 3 active courses for period: '
//                    . $academicPeriod->id . '!');
                dump('Study:' .  $activeStudy->name);
                foreach ($activeStudy->modules as $module) {
//                    dump($module->name);
                }
            }


        }

//
//        /**
//         * Get the study study modules.
//         */
//        public function modules()
//    {
//        // TODO though courses
//    }
//


        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
            $currentStudies = \Scool\EbreEscoolModel\Study::activeOn($academicPeriod->id)->get();
//            if ($academicPeriod->id != 6 && $academicPeriod->id != 7) continue;
            $this->assertTrue($totalStudies > $currentStudies->count(),'There are more current teachers than active teachers!');
            foreach ($currentStudies as $currentStudy) {
//                $this->assertTrue(
//                    is_numeric($code = $currentStudy->details()->activeOn($academicPeriod->id)->first()->code)
//                    || ends_with($code,'S'),
//                    "Teacher code format is incorrect");
//                try {
//                    $department = \Scool\EbreEscoolModel\Department::findOrFail(
//                        $department_id = $currentStudy->details()->activeOn($academicPeriod->id)->first()->department_id);
//                    $this->assertTrue(true);
//                } catch (\Exception $e) {
//                    $this->assertTrue(false, 'Teacher ' . $teacher->name . ' has incorrect department id: ' .
//                        $department_id . '( teacher id: ' . $teacher->id . ')');
//                }

            }
        }
    }
}
