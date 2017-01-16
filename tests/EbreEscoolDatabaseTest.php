<?php

use Scool\EbreEscoolModel\Department;
use Scool\EbreEscoolModel\Study;
use Scool\EbreEscoolModel\Teacher;

/**
 * Class EbreEscoolDatabaseTest
 */
class EbreEscoolDatabaseTest extends TestCase
{
    /**
     * Get all teachers.
     *
     */
    public function teachers()
    {
        return Teacher::all();
    }

    /**
     * Get all departments.
     *
     */
    public function departments()
    {
        return Department::all();
    }

    /**
     * Get all studies.
     *
     */
    public function studies()
    {
        return \Scool\EbreEscoolModel\Study::all();
    }

    /**
     * Boot eloquent.
     */
    public function bootEloquent()
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
        $dotenv->load();

        $capsule->addConnection([
            'host' => env('DB_EBRE_ESCOOL_HOST', 'localhost'),
            'port' => env('DB_EBRE_ESCOOL_PORT', '3306'),
            'driver' => 'mysql',
            'database' => env('DB_EBRE_ESCOOL_DATABASE', 'ebre_escool'),
            'username' => env('DB_EBRE_ESCOOL_USERNAME', 'sergi'),
            'password' => env('DB_EBRE_ESCOOL_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],'ebre_escool');

        $capsule->bootEloquent();
    }

    /**
     * Teachers provider.
     */
    public function teachersProvider()
    {
        $this->bootEloquent();
        $teachers = [];
        foreach ($this->teachers() as $teacher) {
            $teachers[$teacher->name] = [$teacher];
        }
        return $teachers;
    }

    /**
     * Teachers provider by academic period.
     */
    public function teachersProviderByAcademicPeriod()
    {
        $this->bootEloquent();
        $teachersByAP = [];
        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
            $currentTeachers = Teacher::activeOn($academicPeriod->id)->get();
            if ($academicPeriod->id != 6 && $academicPeriod->id != 7) continue;
            foreach ($currentTeachers as $currentTeacher) {
                $teachersByAP[$academicPeriod->name . '_' . $currentTeacher->name] = [
                    $currentTeacher,
                    $academicPeriod->id
                ];
            }
        }
        return $teachersByAP;
    }
    /**
     * Teachers provider by academic period.
     */
    public function teachersTotalsProviderByAcademicPeriod()
    {
        $this->bootEloquent();
        $teachersByAP = [];
        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
            $currentTeachers = Teacher::activeOn($academicPeriod->id)->get();
            $teachersByAP[$academicPeriod->name] = [
                $currentTeachers->count(),
                $academicPeriod->id
            ];
        }
        return $teachersByAP;
    }

    /**
     * Departments provider.
     *
     */
    public function departmentsProvider()
    {
        $departments = $this->departments();
        $departmentsArray = [];
        foreach ($departments as $department) {
            $departmentsArray[$department->name] = [
                $department
            ];
        }
        return $departmentsArray;
    }

    /**
     * Departments by academic period provider.
     */
    public function departmentsByAPProvider()
    {
        $departments = \Scool\EbreEscoolModel\Department::all();
        $departmentsArray = [];
        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
            foreach ($departments as $department) {
                $departmentsArray[$academicPeriod->name . '_' . $department->name ] = [
                    $department,
                    $academicPeriod->id
                ];
            }
        }
        return $departmentsArray;
    }

    /**
     * Studies by academic period provider.
     *
     */
    public function studyModulesByAPProvider()
    {
        $studiesByAp = [];
        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
            $activeStudies = \Scool\EbreEscoolModel\Study::activeOn($academicPeriod->id);
            foreach ($activeStudies->get() as $activeStudy) {
                $studiesByAp[$academicPeriod->name.'_'.$activeStudy->name] = [
                    $activeStudy,
                    $academicPeriod->id
                ];
            }
        }
        return $studiesByAp;
    }

    /**
     * Test total teachers by academic period.
     *
     * @dataProvider teachersTotalsProviderByAcademicPeriod
     * @param $numberOfTeacherByAP
     * @param $periodId
     */
    public function testTotalTeachersByAcademicPeriod($numberOfTeacherByAP, $periodId)
    {
        $allTeachers = $this->teachers();
        $totalTeachers = $allTeachers->count();
        $this->assertTrue($totalTeachers > $numberOfTeacherByAP,
            'There are more current teachers than active teachers for period : ' . $periodId . ' !');
    }

    /**
     * Test teachers are more than 100.
     */
    public function test_teachers_are_more_than_100()
    {
        $allTeachers = $this->teachers();
        $totalTeachers = $allTeachers->count();
        $this->assertTrue($totalTeachers>99, 'There are less than 100 teachers!');
    }

    /**
     * Test teacher.
     *
     * @dataProvider teachersProvider
     * @param Teacher $teacher
     */
    public function testTeacherData(Teacher $teacher)
    {
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

    /**
     * Test teachers data by academic period.
     *
     * @dataProvider teachersProviderByAcademicPeriod
     * @param Teacher $teacher
     * @param $academicPeriodId
     */
    public function testTeachersDataByAcademicPeriod(Teacher $teacher, $academicPeriodId)
    {
        $this->assertTrue(
            is_numeric($code = $teacher->details()->activeOn($academicPeriodId)->first()->code)
            || ends_with($code,'S'),
            "Teacher code format is incorrect");
        try {
            $department = \Scool\EbreEscoolModel\Department::findOrFail(
                $department_id = $teacher->details()->activeOn($academicPeriodId)->first()->department_id);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, 'Teacher ' . $teacher->name . ' has incorrect department id: ' .
                $department_id . '( teacher id: ' . $teacher->id . ')');
        }
    }

    /**
     * Test number of departments.
     *
     */
    public function testNumberOfDepartments()
    {
        $departments = $this->departments();
        $this->assertTrue($departments->count()>4,'There are less than 5 departments!');
    }

    /**
     * Test department data.
     *
     * @dataProvider departmentsProvider
     * @param Department $department
     */
    public function testDepartmentData(Department $department)
    {
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

    /**
     * Test department data by academic period.
     *
     * @dataProvider departmentsByAPProvider
     * @param Department $department
     * @param $academicPeriodId
     */
    public function testDepartmentDataByAP(Department $department, $academicPeriodId)
    {
        $this->assertTrue($department->studiesActiveOn($academicPeriodId)->count() > 0, 'Department ' .
            $department->name . ' does not have any active study for period:' . $academicPeriodId .  '!');
    }

    /**
     * Studies provider.
     */
    public function studiesProvider()
    {
        $studies = $this->studies();

        $studiesArray = [];
        foreach ($studies as $study) {
            $studiesArray[$study->name] = [
                $study
            ];
        }
        return $studiesArray;
    }

    /**
     * Test number of studies.
     */
    public function testNumberOfStudies()
    {
        $studies = $this->studies();
        $totalStudies = $studies->count();
        $this->assertTrue($totalStudies > 19,'There are less than 20 studies!');
    }

    /**
     * Test study.
     *
     * @dataProvider studiesProvider
     * @param \Scool\EbreEscoolModel\Study $study
     *
     */
    public function testStudyData(\Scool\EbreEscoolModel\Study $study)
    {
        $this->assertTrue($study->name != "",
            'Study ' . $study->name . ' has no name! ' . '( study id: ' .
            $study->id . ')');
        $this->assertTrue($study->shortname != "",
            'Study ' . $study->name . ' has no shortname! ' . '( study id: ' .
            $study->id . ')');
        $this->assertTrue($study->allCourses()->count() > 0, 'Study ' . $study->name . '( ' . $study->id. ' )' .
            ' does not have any courses!');
        $this->assertTrue($study->allCourses()->count() < 4 , 'Study ' . $study->name . '( ' . $study->id. ' )' .
            ' have more than 3 courses!');
    }

    /**
     * Test study.
     *
     * @param Study $study
     * @param $academicPeriodId
     * @dataProvider studyModulesByAPProvider
     */
    public function testStudyModulesCount(Study $study, $academicPeriodId)
    {
        $modules = $study->modulesActiveOn($academicPeriodId)->get();
        $modules_count = $modules->count();
        $this->assertTrue($modules_count > 0, 'Study ' . $study->name
            . '( ' . $study->id . ' )' . ' does not have any active modules for period: '
            . $academicPeriodId . ' !');
        $this->assertTrue($study->courses()->count() < 21, 'Study ' . $study->name .
            '( ' . $study->id . ' )' . ' have more than 20 active modules for period: '
            . $academicPeriodId . '!');
    }

}
