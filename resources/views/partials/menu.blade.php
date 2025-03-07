<?php
$menu = \App\Helpers\Qs::menuAccess();
$sub_menu = \App\Helpers\Qs::subMenuAccess();


?>

<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">
    <!-- Sidebar mobile toggler -->
    <div class="sidebar-mobile-toggler text-center">
        <a href="#" class="sidebar-mobile-main-toggle">
            <i class="icon-arrow-left8"></i>
        </a>
        Navigation
        <a href="#" class="sidebar-mobile-expand">
            <i class="icon-screen-full"></i>
            <i class="icon-screen-normal"></i>
        </a>
    </div>
    <!-- /sidebar mobile toggler -->

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user">
            <div class="card-body">
                <div class="media">
                    <div class="mr-3">
                        <a href="{{ route('my_account') }}"><img src="{{ Qs::getDefaultUserImage() }}" width="38" height="38" class="rounded-circle" alt="photo"></a>
                    </div>

                    <div class="media-body">
                        <div class="media-title font-weight-semibold">{{ ucfirst(Auth::user()->name) }}</div>
                        <div class="font-size-xs opacity-50">
                            <i class="icon-user font-size-sm"></i> &nbsp;{{ ucwords(str_replace('_', ' ', Auth::user()->user_type)) }}
                        </div>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ route('my_account') }}" class="text-white"><i class="icon-cog3"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->

        <!-- Main navigation -->
        <div class="card card-sidebar-mobile">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->


                @foreach($menu as $val_menu)
                <li class="nav-item nav-item-submenu">
                    <a href="#" class="nav-link"><i class="{{$val_menu->icon}}"></i><span> {{$val_menu->title}}</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="{{$val_menu->title}}">
                        @foreach($sub_menu as $val_submenu)
                          @if($val_menu->id == $val_submenu->m_parent_code)
                              <li class="nav-item">
                                  <a href="{{url($val_submenu->m_controller_name)}}" class="nav-link">{{$val_submenu->name}}</a>
                              </li>
                          @endif
                        @endforeach
                    </ul>
                </li>
                @endforeach

                {{--Manage Account--}}


                @if(Auth::user()->user_type == 'super_admin')
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['addMainMenu']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-clapboard"></i> <span> Main Menu</span></a>
                        <ul class="nav nav-group-sub" data-submenu-title="Reports">

                            <li class="nav-item"><a href="{{url('/addMainMenu')}}" class="nav-link {{ in_array(Route::currentRouteName(), ['addMainMenu']) ? 'active' : '' }}">Create</a></li>

                        </ul>
                    </li>
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['addSubMenu']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-clapboard"></i> <span> Sub Menu</span></a>
                        <ul class="nav nav-group-sub" data-submenu-title="Reports">

                            <li class="nav-item"><a href="{{url('/addSubMenu')}}" class="nav-link {{ in_array(Route::currentRouteName(), ['addSubMenu']) ? 'active' : '' }}">Create</a></li>

                        </ul>
                    </li>

                @endif

            </ul>
        </div>
    </div>
</div>
