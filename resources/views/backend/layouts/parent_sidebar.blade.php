<!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
        <li class="treeview">
            <a href="{{ URL :: to('/parent/') }}">
                <i class="fa fa-home"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-address-card"></i>
                <span> My Profile </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ URL :: to('/parent/profile') }}">
                        <i class="fa fa-user"></i> <span>View Profile </span></a>
                </li>
                <li><a href="{{ URL :: to('/parent/edit_profile') }}">
                        <i class="fa fa-edit"></i> <span>Edit Profile </span></a>
                </li>
                <li><a href="{{ URL :: to('/parent/change_password') }}">
                        <i class="fa fa-lock"></i> <span>Change password </span></a>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="{{ URL :: to('/parent/getClassroutines') }}">
                <i class="fa fa-calendar-times-o"></i> <span>Class Routine</span>
            </a>
        </li>
        <li class="treeview">
            <a href="{{ URL :: to('/parent/attendance') }}">
                <i class="fa fa-calendar"></i> <span>Attendance</span></a>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-money"></i>
                <span> Accounts </span> <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ URL :: to('/parent/feeBooks') }}">
                        <i class="fa fa-print"></i> <span>Print Fee Book </span></a>
                </li>
                <li><a href="{{ URL :: to('/parent/paymentHistory') }}">
                        <i class="fa fa-bookmark"></i> <span>Payment Reports </span></a>
                </li>
            </ul>
        </li>
        <li class="treeview">
            <a href="{{ URL :: to('/parent/getAcademicResult') }}">
                <i class="fa fa-address-card"></i> <span>Academic Result</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('parent.auth.logout') }}"
               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out"></i> <span> {{ __('Logout') }} </span>
            </a>

            <form id="logout-form" action="{{ route('parent.auth.logout') }}" method="POST"
                  style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</section>

<!-- /.sidebar -->
<script type="text/javascript">
    $(document).ready(function () {

        $('.sidebar ul li').each(function () {
            if (window.location.href.indexOf($(this).find('a:first').attr('href')) > -1) {
                $(this).closest('ul').closest('li').attr('class', 'active');
                $(this).addClass('active').siblings().removeClass('active');
            }
        });

    });
</script>