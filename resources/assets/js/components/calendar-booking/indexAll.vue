<template>
    <div class="main-layout-wrapper">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent m-0">
                <li class="breadcrumb-item">
                    <span>{{ trans('lang.bookings') }}</span>
                </li>
            </ol>
        </nav>
        <div class="main-layout-card">
            <div class="main-layout-card-header-with-button">
                <div class="main-layout-card-content-wrapper">
                    <div class="main-layout-card-header-contents">
                        <h5 class="bluish-text m-0">{{ trans('lang.calendar_booking') }}</h5>
                    </div>
                    <div class="main-layout-card-header-contents text-right d-flex justify-content-end">
                        <div class="p-1">
                            <a href="#" class="btn btn-secondary" @click="tabIndex = 0">
                                {{ trans('lang.day') }}
                            </a> |
                            <a href="#" class="btn btn-secondary" @click="tabIndex = 1">
                                {{ trans('lang.week') }}
                            </a> |
                            <a href="#" class="btn btn-secondary" @click="tabIndex = 2">
                                {{ trans('lang.month') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-layout-card-content">
                <div class="row">
                    <div class="col-4">
                        <h1 class="today-logo" @click="todayClick">TODAY</h1>
                        <h2 class="month text-center">{{whatday.getDate()}}</h2>
                        <div class="calendar">
                            <FullCalendar
                                  defaultView="dayGridMonth"
                                  :locale="locale"
                                  :locales="locales"
                                  :plugins="calendarPlugins"
                                  :showNonCurrentDates="true"
                                  :weekends="true"
                                  :selectable="true"
                                  @dateClick="handleDateClick"
                                  :header="{left: 'prev', center: 'title', right: 'next'}"/>
                        </div>
                    </div>
                    <pre-loader v-if="!timePreLoader"/>
                    <div class="col-8 p-3" v-else>
                        <div class="ml-5" v-if="tabIndex == 0">
                            <h3 class="text-center mb-5">{{dateTitle}}</h3>
                            <div class="ml-5">
                                <div class="d-flex justify-content-between" v-if="services.length">
                                    <p class="text-primary text-center mb-0 service-title" style="flex: 1" v-for="service in services" data-toggle="modal" data-target="#booking-modal" @click="selectedItemId = '', isActive = 1, sId=service.id, date=propDate">{{service.title}}</p>
                                </div>
                                <div class="d-flex justify-content-around" v-else>
                                    <p class="text-center text-danger">There is no service.</p>
                                </div>
                            </div>
                            <div class="service border pt-3 pl-2">
                                <div class="d-flex" v-for="time in 24" :key="time">
                                    <p class="time" style="width: 70px">{{timeFilter(time - 1)}} &nbsp; </p>
                                    <div class="day-books d-flex">
                                        <!-- <div class="book-btn" v-for="(service, i) in services" @click="book(time-1, service.id)"> -->
                                        <div class="book-btn" v-for="(service, i) in services">
                                            <div class="booking" v-if="service.bookings.length">
                                                <div v-for="booking in service.bookings" class="hour-books d-flex justify-content-center" :class="colors[i]">
                                                    <div class="client" v-if="checkBooking(booking, time)" data-toggle="modal" data-target="#booking-modal" @click="selectedItemId = booking.id, isActive = 1">
                                                        <h5 class="text-center">{{booking.booking_time[0]}}</h5>
                                                        <p class="text-center">{{booking.user.first_name}} {{booking.user.last_name}}</p>
                                                        <p class="text-center">{{booking.status[0].toUpperCase() + booking.status.substring(1)}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-5" v-if="tabIndex == 1">
                            <h3 class="text-center mb-5">Week, {{weekTitle}}</h3>
                            <div class="ml-5">
                                <div class="d-flex justify-content-between" v-if="services.length">
                                    <p class="text-primary text-center mb-0" style="flex: 1" v-for="service in services">{{service.title}}</p>
                                </div>
                                <div class="d-flex justify-content-around" v-else>
                                    <p class="text-center text-danger">There is no service.</p>
                                </div>
                            </div>
                            <div class="service border pt-3 pl-2">
                                <div class="d-flex" v-for="(date, index) in getWeekDays(this.whatday)" :key="index">
                                    <p class="time" style="width: 70px">{{date}} &nbsp; {{dayNames[index + 1].substr(0, 3)}}</p>
                                    <div class="week-books d-flex justify-content-around">
                                        <div class="book-btn" v-for="(service, i) in services" @click="choose(date)" :class="checkDate(date, service.service_starting_date, service.service_ending_date) ? colors[i] : ''">
                                            <h5 class="text-center" style="color: white">{{service.title}}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-5" v-if="tabIndex == 2">
                            <h3 class="text-center mb-5">{{weekTitle}}</h3>
                            <div class="ml-5">
                                <div class="d-flex justify-content-between" v-if="services.length">
                                    <p class="text-primary text-center mb-0" style="flex: 1" v-for="service in services">{{service.title}}</p>
                                </div>
                                <div class="d-flex justify-content-around" v-else>
                                    <p class="text-center text-danger">There is no service.</p>
                                </div>
                            </div>
                            <div class="service border pt-3 pl-2">
                                <div class="d-flex" v-for="(date, i) in getMonthLastDay(this.whatday.getFullYear(), this.whatday.getMonth() + 1)" :key="i">
                                    <p class="time" style="width: 40px">{{date}} &nbsp</p>
                                    <div class="month-books d-flex justify-content-around">
                                        <div class="book-btn" v-for="(service, index) in services" @click="choose(date)" :class="checkDate(date, service.service_starting_date, service.service_ending_date) ? colors[index] : ''">
                                            <h5 class="text-center" style="color: white">{{service.title}}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
        <!--Booking modal-->
        <div class="modal fade" id="booking-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered big-modal-dialog" role="document">
                <custombooking-form class="modal-content"
                                   @resetAdminFormModal="resetAdminFormModal"
                                   :modalID="modalID"
                                   :id="selectedItemId"
                                   v-if="isActive == 1"
                                   :sProp="services">
                </custombooking-form>
            </div>
        </div>
    </div>
</template>
<script>
    import 'popper.js';
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    import FullCalendar from '@fullcalendar/vue';
    import dayGridPlugin from '@fullcalendar/daygrid';
    import interactionPlugin from "@fullcalendar/interaction";
    import allLocales from '@fullcalendar/core/locales-all';
    import {VueTelInput} from 'vue-tel-input';
    import {
        setServiceIdToCookie,
        getServiceIdFromCookie,
        deleteServiceIdFromCookie
    } from '../salonLanding/helper/SalonLandingCookieSet.js';

    export default {
        extends: axiosGetPost,
        props: [],
        components: {
            FullCalendar,
            VueTelInput
        },
        data() {
            return {
                locales: allLocales,
                locale: this.trans('lang.calender_language_set'),

                calendarPlugins: [dayGridPlugin, interactionPlugin],
                today: new Date(),
                whatday: new Date(),
                monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                services: [],
                formattedDate: '',
                tabIndex: 0,
                colors: ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary', 'bg-light', 'bg-dark'],
                timePreLoader: false,
                isActive: 0,
                modalID: '#booking-modal',
                service_id: '',
                date: '',
            }
        },
        created() {
            this.todayClick();
        },
        mounted() {
            let instance = this;

            $('#booking-modal').on('hidden.bs.modal', function (e) {
                instance.isActive = 0;
                instance.selectedItemId = '';
            });

            /* If user want alias service link to be auto selected */
            //if(instance.service) instance.serviceBook(instance.service);
            instance.cookieData = getServiceIdFromCookie(instance.appVersion);
            if (instance.cookieData) {
                instance.setCalenderValueFromCookie();
            }
            instance.timePreLoader = true;

            instance.$hub.$on('reloadDataTable', function() {
                instance.start();
            });

            instance.$hub.$on('setActive', function(i) {
                instance.isActive = i;
                $('#booking-modal').modal('hide');
            });

        },
        watch: {
        },
        methods: {
            handleDateClick(arg) {
                this.timePreLoader = false;
                let instance = this;
                this.whatday = arg.date;
                this.formattedDate = arg.dateStr;
                // this.tabIndex = 0;
                this.getServicesBooks('/get-services-books', arg.dateStr);
            },
            start() {
                this.timePreLoader = false;
                // this.whatday = new Date();
                let day = this.whatday.getDate();
                let month = this.whatday.getMonth() + 1;
                let year = this.whatday.getFullYear();
                if (day < 10) day = '0' + day;
                if (month < 10) month = '0' + month;
                this.formattedDate = year + '-' + month +'-' + day;
                this.getServicesBooks('/get-services-books', this.formattedDate);
            },
            setCalenderValueFromCookie() {
                this.service_id = this.cookieData.serviceId;
                this.isFullCalendar = true;
            },
            getServicesBooks(route, date) {
                let instance = this;
                instance.axiosPost(route, {date: date},
                    function (response) {
                        instance.services = response.data;
                        instance.timePreLoader = true;
                    }
                );
            },
            timeFilter: function(i) {
                let mark = 'AM';
                if (i > 12) {
                    i = i - 12;
                    mark = "PM";
                }
                // if (i < 10) {
                //     return ('0' + i + ':00 ' + mark);
                // }
                return (i + ':00 ' + mark);
            },
            book: function(time, service_id) {
                let instance = this;
                let todayNumber = instance.today.getTime();
                let whatdayNumber = instance.whatday.getTime();
                if (whatdayNumber < todayNumber) alert("You can't book at past day!");
                else {
                    $('#booking-modal').modal('show');
                }
            },
            getMonthLastDay: function(year, month) {
                return new Date(year, month, 0).getDate();
            },
            getWeekDays: function(current) {
                var week = new Array();
                // Starting Monday not Sunday
                let temp = new Date(current);
                temp.setDate((temp.getDate() - temp.getDay() + 1));
                for (var i = 0; i < 7; i++) {
                    week.push(
                        new Date(temp).getDate()
                    ); 
                    temp.setDate(temp.getDate() +1);
                }
                return week; 
            },
            choose: function(date) {
                let instance = this;
                instance.whatday = new Date(instance.whatday.getFullYear() + '-' + (instance.whatday.getMonth() + 1) + '-' + date); // date + 1 : timezone problem
                instance.tabIndex = 0;
            },
            checkDate: function(date, startDay, endDay) {
                let instance = this;
                let thisDay = new Date(instance.whatday.getFullYear() + '-' + (instance.whatday.getMonth() + 1) + '-' + date).getTime();
                return (thisDay >= new Date(startDay).getTime() && thisDay <= new Date(endDay).getTime()) ? true : false;
            },
            resetAdminFormModal() {
                this.isActive = 0;
            },
            checkBooking(booking, time) {
                return this.formattedDate == booking.booking_date && booking.booking_time[0].split(':')[0] == this.timeFilter(time - 1).split(':')[0] && booking.booking_time[0].split(' ')[1] == this.timeFilter(time - 1).split(' ')[1];
            },
            todayClick() {
                this.whatday = new Date();
                this.start();
            }
        },
        computed: {
            dateTitle() {
                let instance = this;
                return instance.dayNames[instance.whatday.getDay()] + ', ' + instance.monthNames[instance.whatday.getMonth()] + ' ' + instance.whatday.getDate() + ', ' + instance.whatday.getFullYear();
            },
            weekTitle() {
                let instance = this;
                return instance.monthNames[instance.whatday.getMonth()] + ', ' + instance.whatday.getFullYear();
            },
            propDate() {
                let instance = this;
                return instance.whatday.getDate() + '/' + (instance.whatday.getMonth() + 1) + '/' + instance.whatday.getFullYear();
            }
        }
    }
</script>
<style scoped>
    .main-layout-card-content {
        padding-top: 0;
    }
    .today-logo {
        border: 1px solid grey;
        border-radius: 35px;
        padding: 10px 20px;
        width: 155px;
        font-size: 32px;
        font-weight: bold;
        text-align: center;
        color: white;
        background-image: linear-gradient(338deg, black, transparent);
        box-shadow: 5px 5px 8px -2px black;
        margin-block-end: 0;
        margin-bottom: 0;
    }
    .today-logo:hover {
        cursor: pointer;
    }
    .service-title:hover {
        cursor: pointer;
    }
    .month {
        font-size: 150px;
    }
    .time {
        margin-top: -12px;
    }
    .day-books, .week-books, .month-books {
        border-top: 1px solid lightgrey;
        flex: 1;
    }
    .day-books {
        height: 70px;
    }
    .booking {
        height: 70px;
        overflow-y: auto;
    }
    .week-books {
        height: 70px;
    }
    .month-books {
        height: 50px;
    }
    .book-btn {
        flex: 1;
    }
    .week-books .book-btn, .month-books .book-btn {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .book-btn .client:hover {
        cursor: pointer;
    }
    .hour-books {
        margin-right: 5px;
        background-color: lightgrey;
    }
    .client {
        border-bottom: 1px dotted white;
    }
    .client h5, .client p {
        margin-bottom: 0;
        color: white;
    }
    .service {
        height: calc(100vh - 340px);
        overflow-y: auto;
    }
</style>