<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
        <li class="treeview">
            <a href="{{ URL :: to('/admin/dashboard') }}">
                <i class="fa fa-home"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-users"></i>
                <span>Academic Users </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ URL :: to('/admin/students') }}">
                        <i class="mdi mdi-account-group"></i> <span>Students</span></a>
                </li>
                <li><a href="{!! route('admin.students.promotion') !!}">
                        <i class="mdi mdi-account-group"></i> <span>Students Promotion</span></a>
                </li>
                <li>
                    <a href="{{ URL :: to('/admin/parents') }}">
                        <i class="mdi mdi-account-badge"></i> <span>Parents</span>
                    </a>
                </li>
                <li>
                    <a href="{{ URL :: to('/admin/teachers') }}">
                        <i class="mdi mdi-account-badge"></i> <span>Teachers</span>
                    </a>
                </li>
                <li><a href="{{ URL :: to('/admin/staffs') }}">
                        <i class="mdi mdi-account"></i> <span>Staff</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-address-card"></i>
                <span>Academic </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ URL :: to('/admin/stdclasses') }}">
                        <i class="fa fa-building"></i> <span>Classes</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/classrooms') }}">
                        <i class="fa fa-building-o"></i> <span>Class Rooms</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/classroutines') }}">
                        <i class="fa fa-address-card"></i> <span>Class Routines</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/sections') }}">
                        <i class="fa fa-building"></i> <span>Sections</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/subjects') }}">
                        <i class="fa fa-book"></i> <span>Subjects</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/syllabus') }}">
                        <i class="fa fa-book"></i> <span>Syllabus</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/academiccalenders') }}">
                        <i class="fa fa-calendar"></i> <span>Academic Calender</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/events') }}">
                        <i class="fa fa-calendar-o"></i> <span>Event Calender</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/viewCalender') }}">
                        <i class="fa fa-calendar-o"></i> <span>View Calender</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-address-book"></i>
                <span>Online Admission </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ URL :: to('/admin/admissionApplication') }}">
                        <i class="fa fa-book"></i> <span>Online Applications</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/admissionResult') }}">
                        <i class="fa fa-upload"></i> <span>Admission Result</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-address-card"></i>
                <span>Result Management</span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ URL :: to('/admin/exams') }}">
                        <i class="fa fa-address-book"></i> <span>Exam Routines</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/assignExaminee') }}">
                        <i class="fa fa-check-square"></i> <span>Assign Examinee</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/admitCard') }}">
                        <i class="fa fa-address-card"></i> <span>Admit Card</span></a>
                </li>
                {{--<li><a href="{{ URL :: to('/admin/grades') }}">--}}
                {{--<i class="fa fa-pie-chart"></i> <span>Grades</span></a>--}}
                {{--</li>--}}
                <li><a href="{{ URL :: to('/admin/marks') }}">
                        <i class="fa fa-sort-numeric-asc"></i> <span>Marks</span></a>
                </li>
                <li class="treeview">
                    <a href="{{ URL :: to('/admin/jrhalfYearlyExam') }}">
                        <i class="fa fa-address-card"></i>
                        <span>Class 6, 7, 8 Result</span> 
                        <!--<i class="fa fa-angle-left pull-right"></i>-->
                    </a>
                    <!--<ul class="treeview-menu">-->
                    <!--    <li>-->
                    <!--        <a href="{{ URL :: to('/admin/jrhalfYearlyExam') }}">-->
                    <!--            <i class="fa fa-bar-chart-o"></i> <span>Half Yearly Exam</span></a>-->
                    <!--    </li>-->
                    <!--    <li>-->
                    <!--        <a href="{{ URL :: to('/admin/jrfullMarksheet') }}">-->
                    <!--            <i class="fa fa-bar-chart-o"></i> <span>Annual Exam</span></a>-->
                    <!--    </li>-->
                    <!--</ul>-->
                </li>
                <li>
                    <a href="{{ URL :: to('/admin/srResult') }}">
                        <i class="fa fa-bar-chart-o"></i> <span>Class 9, 10 Result</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-money"></i>
                <span>Accounts Management </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-edit"></i>
                        <span> Student Fee </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ URL :: to('/admin/accountsHead') }}">
                                <i class="fa fa-edit"></i><span>Accounts Head</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/accountsFees') }}">
                                <i class="fa fa-edit"></i><span>Fees Management</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/accountsExceptionalStudent') }}">
                                <i class="fa fa-user"></i><span>Exceptional Student</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/printFeePdf') }}">
                                <i class="fa fa-print"></i><span>Print Fee Book</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/studentPayment') }}">
                                <i class="fa fa-money"></i><span>Student Payments</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/accountsFeePaymentHistory') }}">
                                <i class="fa fa-print"></i><span>Student Fee Reports</span></a>
                        </li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-edit"></i>
                        <span> Incomes </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ URL :: to('/admin/allIncomes') }}">
                                <i class="fa fa-credit-card"></i> <span>All Incomes</span></a>
                        </li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-edit"></i>
                        <span> Expenses </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ URL :: to('/admin/allExpenses') }}">
                                <i class="fa fa-credit-card"></i> <span>All Expenses</span></a>
                        </li>
                    </ul>
                </li>
                <li><a href="{{ URL :: to('/admin/bankAccounts') }}">
                        <i class="fa fa-bank"></i> <span>Bank Account Setup</span></a>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-print"></i>
                        <span> Reports </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ URL :: to('/admin/incomeStatement') }}">
                                <i class="fa fa-print"></i> <span>Income Statement</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/expenseStatement') }}">
                                <i class="fa fa-print"></i> <span>Expense Statement</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-area-chart"></i>
                <span>Attendance </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-edit"></i>
                        <span>Student Attendance </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ URL :: to('/admin/importStdattendances') }}">
                                <i class="fa fa-circle-o"></i><span>Import Daily Attendance</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/importStdAttendanceMonthly') }}">
                                <i class="fa fa-circle-o"></i><span>Import Monthly Attendance</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/studentDailyAttendanceReport') }}">
                                <i class="fa fa-circle-o"></i><span>Daily Report</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/studentMonthlyAttendanceReport') }}">
                                <i class="fa fa-circle-o"></i><span>Monthly Report</span></a>
                        </li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-edit"></i>
                        <span>Teacher Attendance </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ URL :: to('/admin/importTeacherattendances') }}">
                                <i class="fa fa-circle-o"></i><span>Import Daily Attendance</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/importTeacherAttendanceMonthly') }}">
                                <i class="fa fa-circle-o"></i><span>Import Monthly Attendance</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/teacherDailyAttendanceReport') }}">
                                <i class="fa fa-circle-o"></i><span>Daily Report</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/teacherMonthlyAttendanceReport') }}">
                                <i class="fa fa-circle-o"></i><span>Monthly Report</span></a>
                        </li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-edit"></i>
                        <span>Staff Attendance </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ URL :: to('/admin/importStaffattendances') }}">
                                <i class="fa fa-circle-o"></i><span>Import Daily Attendance</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/importStaffAttendanceMonthly') }}">
                                <i class="fa fa-circle-o"></i><span>Import Monthly Attendance</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/staffDailyAttendanceReport') }}">
                                <i class="fa fa-circle-o"></i><span>Daily Report</span></a>
                        </li>
                        <li><a href="{{ URL :: to('/admin/staffMonthlyAttendanceReport') }}">
                                <i class="fa fa-circle-o"></i><span>Monthly Report</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-book"></i>
                <span>Library </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ URL :: to('/admin/books') }}">
                        <i class="fa fa-book"></i> <span>Books</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/bookrequests') }}">
                        <i class="fa fa-address-book"></i> <span>Issue Book</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview" style="display: none">
            <a href="#">
                <i class="fa fa-envelope"></i>
                <span>Message</span><i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-circle-o"></i>
                        <span>Send Messages </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href=""><i
                                    class="fa fa-circle-o"></i> Send To Teacher</a></li>
                        <li><a href=""><i
                                    class="fa fa-circle-o"></i> Send To Parents</a></li>
                        <li><a href=""><i
                                    class="fa fa-circle-o"></i> Send To Student</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-circle-o"></i>
                        <span>Received Messages </span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href=""><i
                                    class="fa fa-circle-o"></i> Received From Teacher</a></li>
                        <li><a href=""><i
                                    class="fa fa-circle-o"></i> Received From Parents</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-university"></i>
                <span>Frontend </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ URL :: to('/admin/news') }}">
                        <i class="fa fa-globe"></i> <span> Notice Board & News</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/sliders') }}"><i
                            class="fa fa-sliders"></i> Slider </a>
                </li>
                <li><a href="{{ URL :: to('/admin/downloads') }}"><i
                            class="fa fa-download"></i> Digital Content </a>
                </li>
                <li><a href="{{ URL :: to('/admin/galleries') }}"><i
                            class="fa fa-image"></i> Gallery </a>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="{{ URL :: to('/admin/sommelon') }}">
                <i class="fa fa-home"></i> <span>Student Alumni</span>
            </a>
        </li>
        <li class="treeview">
            <a href="{{ URL :: to('/admin/settings') }}">
                <i class="fa fa-cogs"></i> <span>Settings</span>
            </a>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-universal-access"></i>
                <span>Access Management </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ URL :: to('/admin/users') }}">
                        <i class="fa fa-users"></i> <span>Operators</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/permissions') }}">
                        <i class="fa fa-book"></i> <span>Permissions</span></a>
                </li>
                <li><a href="{{ URL :: to('/admin/roles') }}"><i
                            class="fa fa-bookmark"></i> Roles </a>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="{{ URL :: to('/admin/backups') }}">
                <i class="fa fa-cloud-download"></i> <span>Backup</span>
            </a>
        </li>
    </ul>
</section>

<!-- /.sidebar -->
<script type="text/javascript">
    $(document).ready(function () {
        $('.sidebar ul li').each(function () {
            if (window.location.href.indexOf($(this).find('a:first').attr('href')) > -1) {
                $(this).closest('ul').closest('li').attr('class', 'active');
                $(this).closest('ul').closest('li').closest('ul').closest('li').attr('class', 'active');
                $(this).addClass('active').siblings().removeClass('active');
            }
        });
    });
</script>