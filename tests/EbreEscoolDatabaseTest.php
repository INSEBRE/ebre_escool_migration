<?php

use Scool\EbreEscoolModel\Course;
use Scool\EbreEscoolModel\Department;
use Scool\EbreEscoolModel\Person;
use Scool\EbreEscoolModel\Study;
use Scool\EbreEscoolModel\StudyModule;
use Scool\EbreEscoolModel\StudyModuleSubtype;
use Scool\EbreEscoolModel\StudyModuleType;
use Scool\EbreEscoolModel\StudySubModule;
use Scool\EbreEscoolModel\Teacher;
use Scool\EbreEscoolModel\User;

/**
 * Class EbreEscoolDatabaseTest
 */
class EbreEscoolDatabaseTest extends TestCase
{
    /**
     * EbreEscoolDatabaseTest constructor.
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name,$data, $dataName);
        $this->bootEloquent();
    }

    /**
     * Get all teachers.
     *
     */
    public function teachers()
    {
        return Teacher::all();
    }

    /**
     * Get all teachers.
     *
     */
    public function activeTeachers()
    {
        return Teacher::active();
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
        return Study::all();
    }

    /**
     * Get all courses.
     *
     */
    public function courses()
    {
        return Course::all();
    }

    /**
     * Get all modules.
     *
     */
    public function modules()
    {
        return StudyModule::all();
    }

    /**
     * Get all active modules.
     *
     */
    public function activeModules()
    {
        return StudyModule::active();
    }

    /**
     * Get all submodules.
     *
     */
    public function submodules()
    {
        return StudySubModule::all();
    }

    /**
     * Get all active submodules.
     *
     */
    public function activeSubmodules()
    {
        return StudySubModule::active();
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
        $teachers = [];
        foreach ($this->teachers() as $teacher) {
            $teachers[$teacher->name] = [$teacher];
        }
        return $teachers;
    }

    /**
     * Active teachers provider.
     */
    public function activeTeachersProvider()
    {
        $teachers = [];
        foreach ($this->activeTeachers()->get() as $teacher) {
            $teachers[$teacher->name] = [$teacher];
        }
        return $teachers;
    }

    /**
     * Teachers provider by academic period.
     */
    public function teachersProviderByAcademicPeriod()
    {
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
     * Studies by academic period provider.
     *
     */
    public function studiesByAPProvider()
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
     * Studies provider.
     */
    public function coursesProvider()
    {
        $courses = $this->courses();

        $coursesArray = [];
        foreach ($courses as $course) {
            $coursesArray[$course->name] = [
                $course
            ];
        }
        return $coursesArray;
    }

    /**
     * Modules provider.
     */
    public function modulesProvider()
    {
        $modules = $this->modules();

        $modulesArray = [];
        foreach ($modules as $module) {
            $modulesArray[$module->name] = [
                $module
            ];
        }
        return $modulesArray;
    }

    /**
     * Active modules provider.
     */
    public function activeModulesProvider()
    {
        $modules = $this->activeModules()->get();
        $modulesArray = [];
        foreach ($modules as $module) {
            $modulesArray[$module->name] = [
                $module
            ];
        }

        return $modulesArray;
    }

    /**
     * Study modules by academic period provider.
     *
     */
    public function modulesByAPProvider()
    {
        $modulesByAP = [];
        foreach (\Scool\EbreEscoolModel\AcademicPeriod::all() as $academicPeriod) {
            $activeModules = StudyModule::activeOn($academicPeriod->id);
            foreach ($activeModules->get() as $activeModule) {
                $modulesByAP[$academicPeriod->name.'_'.$activeModule->name] = [
                    $activeModule,
                    $academicPeriod->id
                ];
            }
        }
        return $modulesByAP;
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
    public function testTeachersAreMoreThan100()
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
     *
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
        $this->assertInstanceOf(Person::class, $teacher->person,
            'Person object associated to Teacher ' . $teacher->name . ' is not an instance of Person! ' . '( teacher id: ' .
            $teacher->id . ')');
        $this->assertTrue($teacher->user != null,
            'Teacher ' . $teacher->name . ' has no user data! ' . '( teacher id: ' .
            $teacher->id . ')');
        $this->assertInstanceOf(User::class, $teacher->user,
            'User object associated to Teacher ' . $teacher->name . ' is not an instance of User! ' . '( teacher id: ' .
            $teacher->id . ')');
        $this->assertTrue($teacher->user->person_id == $teacher->person->id,
            'Teacher ' . $teacher->name . ' has incoherent data! ' . '( teacher id: ' .
            $teacher->id . ')');
    }

    /**
     * Test active teacher.
     *
     * @dataProvider activeTeachersProvider
     * @param Teacher $teacher
     *
     */
    public function testActiveTeacher(Teacher $teacher)
    {
        $this->assertTrue($teacher->active,
            'Teacher ' . $teacher->name . ' is not active! ' . '( teacher id: ' .
            $teacher->id . ')');
        $this->assertInstanceOf(Department::class, $teacher->department,
            'Department for teacher' . $teacher->name . ' is not an instance of Department class! ' . '( teacher id: ' .
            $teacher->id . ')'
        );
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
        $department_id = null;
        try {
            \Scool\EbreEscoolModel\Department::findOrFail(
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
        $this->assertInstanceOf(Teacher::class, $department->headOfDepartment,
            'Department ' . $department->name . ' has no valid instance of department head! ' . '( department id: ' .
            $department->id . ')');
        try {
            $head = Teacher::findOrFail($department->head);
            $this->assertTrue(true);
            $this->assertTrue($head->active,'Department ' . $department->name . ' has not active head id: ' .
                $department->head . '( department id: ' . $department->id . ')');
            $this->assertInstanceOf(Teacher::class, $head,
                'Department ' . $department->name . ' has not a valid instance for head: ' .
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
     * Test number of studies.
     */
    public function testNumberOfStudies()
    {
        $studies = $this->studies();
        $totalStudies = $studies->count();
        $this->assertTrue($totalStudies > 19,'There are less than 20 studies!');
    }

    /**
     * Test study data.
     *
     * @dataProvider studiesProvider
     * @param \Scool\EbreEscoolModel\Study $study
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
     * Test study modules count.
     *
     * @param Study $study
     * @param $academicPeriodId
     * @dataProvider studiesByAPProvider
     * @group error
     */
    public function testStudyModulesCount(Study $study, $academicPeriodId)
    {
        $modules = $study->modulesActiveOn($academicPeriodId)->get();
        $modules_count = $modules->count();
        $this->assertTrue($modules_count > 0, 'Study ' . $study->name
            . '( ' . $study->id . ' )' . ' does not have any active modules for period: '
            . $academicPeriodId . ' !');
        //TODO: moduls d'un studi repetits pq es fan a primer i segon i per tant tenen horari diferent!
        if ($modules_count > 20) {
            dump('period: ' . $academicPeriodId);
            dump($study->name);
            dump(' count: ' . $modules_count);
            $i = 1;
            foreach ($modules as $module) {
                dump($i . ' ' . $module->name . ' id: ' . $module->id);
                $i++;
            }
        }
        $this->assertTrue($modules_count < 21, 'Study ' . $study->name .
            '( ' . $study->id . ' )' . ' have more than 20 active modules for period: '
            . $academicPeriodId . '!');
    }

    /**
     * Test study modules data.
     *
     * @param Study $study
     * @param $academicPeriodId
     * @dataProvider studiesByAPProvider
     * @group failing
     */
    public function testStudyModulesData(Study $study, $academicPeriodId)
    {
        dump($academicPeriodId . ' - ' . $study->name . '( ' . $study->id . ' )');
        $courses = $study->coursesActiveOn($academicPeriodId)->pluck('course_id');
        $courses_names = $study->coursesActiveOn($academicPeriodId)->pluck('course_name');
        dump($courses->toArray());
        dump($courses_names->toArray());
        $modules = $study->modulesActiveOn($academicPeriodId)->orderBy('study_module_shortname')->get();
        $i=1;
        dump('Modules count: ' . $modules->count() );
        foreach ($modules as $module) {
            dump(' Module: ' . $i . ' | ' . $module->order . ' ' . $module->shortname . ' ' . $module->name . '('. $module->id .')');
            $this->assertTrue($i === $module->order,
                'Order (' . $module->order . ') for module ' . $module->shortname . ' ' . $module->name . '('.  $module->id .')' .
                '. Study: ' . $study->name . ' | Academic Period: ' . $academicPeriodId  .
                ' . Expected order : ' .  $i);
            $i++;
        }
    }

    /**
     * Test Study courses.
     *
     * @dataProvider studiesByAPProvider
     * @param Study $study
     *
     */
    public function testStudyCourses(Study $study, $academicPeriodId)
    {
        // 29 -> ASIX
        // 31 -> DAM
        // 2 - > ASIX-DAM
        // 14 -> Soldadura i caldereria algun any només 1 curs
        // 16 -> PM curs de dual!
//        $studiesToSkip = [ 29, 31 , 2 , 14, 9, 16, 40 ];
        $studiesToSkip = [ [9,1] , [16,1], [29,1], [9,2], [16,2], [29,2],[9, 3] , [16,3] , [29,3]
            , [31,3] , [2,4], [9,4], [14,4], [16,4] , [2,5] , [9,5] , [14,5], [16,5], [2,6], [9,6]
            , [6,6], [14,6], [16,6], [29,6] , [31,6], [2,6], [2,7] , [31,7], [29,7] , [14,7],
            [16,7], [40,7], [12,2], [12,3], [12,4], [12,5], [12,6], [12,7]];

        if (in_array([$study->id,$academicPeriodId],$studiesToSkip)) return;
        $i=1;
        foreach ($study->allCourses as $course) {
            $this->assertTrue(str_contains($course->name,$i),
                'Course ' . $course->name . ' for study ' . $study->name .
                ' order does not corresponds to expected value : ' . $i
            );
            $calculatedName = 'Curs ' . $i . ' - ' . $study->shortname;
            $this->assertTrue($calculatedName == $course->name,
                'Course ' . $course->name . ' for study ' . $study->name .
                ' name does not corresponds to expected value : ' . $calculatedName
            );
            $i++;
        }
    }

    /**
     * Test courses.
     *
     * @param Course $course
     * @dataProvider coursesProvider
     *
     */
    public function testCourseData(Course $course)
    {
        $this->assertTrue($course->name != "",
            'Course ' . $course->name . ' has no name! ' . '( course id: ' .
            $course->id . ')');
        $this->assertTrue($course->shortname != "",
            'Course ' . $course->name . ' has no shortname! ' . '( course id: ' .
            $course->id . ')');
        $this->assertTrue($course->number != null,
            'Course ' . $course->name . ' has no number! ' . '( course id: ' .
            $course->id . ')');
        $this->assertTrue($course->study_id != null,
            'Course ' . $course->name . ' has no study_id! ' . '( course id: ' .
            $course->id . ')');
        $study_id =  null;
        try {
            Study::findOrFail(
                $study_id = $course->study_id);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, 'Course ' . $course->name . ' has incorrect study id: ' .
                $study_id . '( course id: ' . $course->id . ')');
        }
        $this->assertInstanceOf(Study::class, $course->study,
            'Course ' . $course->name . ' has not a valid instance of study! ' . '( course id: ' .
            $course->id . ')');
    }

    /**
     * Test study module data.
     *
     * @param StudyModule $module
     * @dataProvider modulesProvider
     * @group workingon
     */
    public function testModuleData(StudyModule $module)
    {
        dump($module->id);
        dump($module->name);
        dump($module->shortname);
        dump($module->study_shortname);
//        dump($module->description);
//        dump($module->type);
//        dump($module->subtype);
//        dump($module->hoursPerWeek);
//        dump($module->order);

        $this->assertTrue($module->name != "",
            'Module ' . $module->name . ' has no name! ' . '( module id: ' .
            $module->id . ')');
        $this->assertTrue($module->shortname != "",
            'Module ' . $module->name . ' has no shortname! ' . '( module id: ' .
            $module->id . ')');
        $this->assertTrue($module->study_shortname != "",
            'Module ' . $module->name . ' has no study_shortname! ' . '( module id: ' .
            $module->id . ')');
//        $this->assertInstanceOf(Study::class,$module->study(),
//            'Module ' . $module->name . ' has not valid instance of study! ' . '( module id: ' .
//            $module->id . ')');
        $expectedShortname = 'MP' . str_pad($module->order, 2, "0", STR_PAD_LEFT);
        $this->assertTrue($expectedShortname == $module->shortname,
            'Module ' . $module->name . ' shortname is not formatted as expected 
                (Expected: ' . $expectedShortname . ' Found: ' . $module->shortname . ' ) ' .
            ' ! ' . '( module id: ' .
            $module->id . ')');
    }

//    /**
//     * Test study module data by academic period.
//     *
//     * @dataProvider modulesByAPProvider
//     * @param StudyModule $module
//     * @param $academicPeriodId
//     * @group workingon1
//     */
//    public function testModuleDataByAP(StudyModule $module, $academicPeriodId)
//    {
//        dump($academicPeriodId);
//        dump($module->id);
//        dump($module->name);
//        dump($module->shortname);
//        dump($module->study_shortname);
//        $this->assertInstanceOf(Study::class,$module->study(),
//            'Module ' . $module->name . ' has not valid instance of study ' .
//            ' for period ' . $academicPeriodId . ' ! ' . '( module id: ' .
//            $module->id . ')');
//    }

    /**
     * Test active study module.
     *
     * @dataProvider activeModulesProvider
     * @param StudyModule $module
     * @group workingon2
     */
    public function testActiveModules(StudyModule $module)
    {
//        dump($module->id);
//        dump($module->name);
//        dump($module->shortname);
//        dump($module->study_shortname);
//        dump($module->hoursPerWeek);
//        dump($module);
//        dump($module->type);
//        dump($module->subtype);
//        dump($module->totalHours);
        $study = $module->study();

        $this->assertInstanceOf(StudyModuleType::class,$module->type,
            'Module ' . $module->name . ' has not valid instance of study type' .
            ' ! ' . '( module id: ' .
            $module->id . ')');

        //TODO: subtype serveix per alguna cosa? tenim taula buida!
//        $this->assertInstanceOf(StudyModuleSubtype::class,$module->subtype,
//            'Module ' . $module->name . ' has not valid instance of study subtype' .
//            ' ! ' . '( module id: ' .
//            $module->id . ')');

        //TODO introduce total Hours (utilitzar any actual passant de dades històriques)
//        $this->assertTrue($module->totalHours != 0);
        //Idem anterior:
//        $this->assertTrue($module->hoursPerWeek != 0);

//        $this->assertInstanceOf(Study::class,$study,
//            'Module ' . $module->name . ' has not valid instance of study ' .
//            ' ! ' . '( module id: ' .
//            $module->id . ')');
//        $study_shortname = $study->shortname;

//        $this->assertTrue($module->study_shortname == $study_shortname,
//            'Module ' . $module->name . ' has incoherence in study_shortname filed ( Expected: ' .
//            $module->study_shortname .  ' Found: ' . $study_shortname . ' ) ' .
//            ' ! ' . '( module id: ' .
//            $module->id . ')');
    }
}
