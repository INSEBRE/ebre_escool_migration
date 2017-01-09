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
    public function testTeacher()
    {
        $allTeachers = Teacher::all();
        $totalTeachers = $allTeachers->count();
        $this->assertTrue($totalTeachers>100,'There are less than 100 teachers!');

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
}
