<template>
    <div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="point">{{ trans('lang.point_status') }}</label>
                <input id="point"
                       v-model="point"
                       type="number"
                       name="point"
                       class="form-control"
                       disabled>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" data-toggle="modal" data-target="#buyPointModal">{{ trans('lang.buy_point') }}</button>
            </div>
        </div>


        <!--Payment Modal-->
        <div class="modal modal-landing fade" id="buyPointModal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-body justify-content-center">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="new-point">Point</label>
                                    <input type="number" name="new-point" id="new-point" v-model="newPoint" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="price">Price</label>
                                    <input type="text" name="price" id="price" v-model="price" class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div v-if="!checkPaymentMethod(paymentMethods)">
                                    {{ trans('lang.please_choose_a_payment_method') }}:
                                </div>
                                <div class="payment-methods-button-container d-flex justify-content-around" v-for="paymentMethod in paymentMethods">
                                    <a href="#"
                                       class="payment-methods-btn center-align"
                                       v-if="paymentMethod.type=='stripe' && paymentMethod.available_to_client==1 && paymentMethod.enable==1 && paymentMethod.option"
                                       :class="{'selectedPaymentMethod': paymentMethodClicked==paymentMethod.title}"
                                       @click.prevent="paymentMethodClicked = paymentMethod.title,isPaymentMethodDefault=true,selectedMethodId=paymentMethod.id,paymentMethodType=paymentMethod.type">
                                        {{paymentMethod.title}}
                                    </a>
                                    <a href="#"
                                       class="payment-methods-btn center-align"
                                       v-if="paymentMethod.type=='paypal' && paymentMethod.available_to_client==1 && paymentMethod.enable==1 && paymentMethod.option"
                                       :class="{'selectedPaymentMethod': paymentMethodClicked==paymentMethod.title}"
                                       @click.prevent="paymentMethodClicked = paymentMethod.title,isPaymentMethodDefault=true,selectedMethodId=paymentMethod.id,paymentMethodType=paymentMethod.type">
                                        {{paymentMethod.title}}
                                    </a>
                                    <a href="#"
                                       class="payment-methods-btn center-align"
                                       v-else-if="paymentMethod.type!='stripe' && paymentMethod.type!='paypal' && paymentMethod.available_to_client==1 && paymentMethod.enable==1"
                                       :class="{'selectedPaymentMethod': paymentMethodClicked==paymentMethod.title}"
                                       @click.prevent="paymentMethodClicked = paymentMethod.title,isPaymentMethodDefault=false,selectedMethodId=paymentMethod.id,paymentMethodType=paymentMethod.type">
                                        {{paymentMethod.title}}
                                    </a>
                                </div>

                                <div class="row" v-if="!checkPaymentMethod(paymentMethods)">
                                    <div class="col-12">
                                        <div class="user-info-button">
                                            <button v-if="isPaymentMethodDefault"
                                                    class="btn btn-primary app-color mobile-btn"
                                                    id="default"
                                                    type="submit" :disabled="!paymentMethodClicked"
                                                    @click.prevent="buyPoint">
                                                {{ trans('lang.confirm') }}
                                            </button>
                                            <button v-else
                                                    class="btn btn-primary app-color mobile-btn"
                                                    type="submit"
                                                    :disabled="!paymentMethodClicked"
                                                    @click.prevent="buyPoint">
                                                {{ trans('lang.confirm') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-else>
                                    <div class="col-12">
                                        <div class="user-info-button">
                                            <button class="btn btn-primary app-color mobile-btn"
                                                    type="submit"
                                                    @click.prevent="buyPoint">
                                                {{ trans('lang.confirm') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="stripeModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body justify-content-center">
                        <div class='form-row row'>
                            <div class='col-xs-12 col-md-6 form-group required'>
                                <label class='control-label'>Name on Card</label>
                                <input class='form-control' size='4' v-model="cardName" type='text'>
                            </div>
                            <div class='col-xs-12 col-md-6 form-group required'>
                                <label class='control-label'>Card Number</label>
                                <input class='form-control card-number' v-model="cardNumber" size='20' type='text'>
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-xs-12 col-md-4 form-group cvc required'>
                                <label class='control-label'>CVC</label>
                                <input class='form-control card-cvc' placeholder='ex. 311' v-model="cardCvc" size='4' type='text'>
                            </div>
                            <div class='col-xs-12 col-md-4 form-group expiration required'>
                                <label class='control-label'>Expiration Month</label>
                                <input class='form-control card-expiry-month' placeholder='MM' size='2' v-model="expiryMonth" type='text'>
                            </div>
                            <div class='col-xs-12 col-md-4 form-group expiration required'>
                                <label class='control-label'>Expiration Year</label>
                                <input class='form-control card-expiry-year' placeholder='YYYY' size='4' v-model="expiryYear" type='text'>
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-md-12 error form-group' v-if="stripeError">
                                <div class='alert-danger alert'>Please correct the errors and try again.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <button class="btn btn-primary btn-lg btn-block" @click="pay" style="margin-top:20px;" >Pay Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Authentication warning modal-->
        <div class="modal fade" id="paypalModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content px-4 pb-4 pt-3 text-center">
                    <div class="row">
                        <div class="col-12">
                            <div class="payment-methods-button-container">
                                <div id="paypal-button-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        props: [
            'profile',
            'paymentMethods',
        ],
        extends: axiosGetPost,
        components: {
            Datepicker
        },
        data() {
            return {
                point: 0,
                isActive: 0,
                point: 0,
                paymentMethodClicked: '',
                isPaymentMethodDefault: false,
                newPoint: 0,
                cardName:'',
                cardNumber:'',
                cardCvc:'',
                expiryMonth:'',
                expiryYear:'',
                stripeToken:'',
                stripeError: false,
                paypalError: false
            }
        },
        mounted() {
            let instance = this;
            instance.point = instance.profile.point;

            $('#due-payment-modal').on('show.bs.modal', function (e) {
                instance.isDuePaymentModalActive = true;
            });

            $('#due-payment-modal').on('hidden.bs.modal', function (e) {
                instance.isDuePaymentModalActive = false;
            });
        },

        methods: {
            customFormatter(date) {
                return moment(date).format(this.dateFormat);
            },
            buyPoint() {
                if (this.newPoint > 0) {
                    if (this.paymentMethodClicked == 'Stripe') {
                        this.isActive = 1;
                    } else if (this.paymentMethodClicked == 'PayPal') {
                        this.isActive = 2;
                    } else {
                        this.isActive = 3;
                    }
                    $('#buyPointModal').modal('hide');
                    if (this.isActive == 1) {
                        $('#stripeModal').modal('show');
                    }
                    
                } else {
                    alert("You didn't input the number of points you want!");
                }
            },
            checkPaymentMethod(paymentMethods) {
                if (_.differenceWith(paymentMethods, [{
                    'available_to_client': 1,
                    'enable': 1
                }], _.isEqual).length == 0) {
                    return true;
                } else {
                    return false;
                }
            },
            pay() {
                if (this.isActive == 1) {
                  // Stripe.createToken({
                  //   number: this.cardNumber,
                  //   cvc: this.cardCvc,
                  //   exp_month: this.expiryMonth,
                  //   exp_year: this.expiryYear
                  // }, this.stripeResponseHandler);
                  let instance = this;
                  let publishable_key = '';
                  instance.paymentMethods.forEach(function (element) {
                        if (element.type == 'stripe') {
                            publishable_key = element.option;
                        }
                    });
                  this.handler = StripeCheckout.configure({
                        key: publishable_key,
                        locale: 'auto',
                        image: instance.publicPath + '/images/stripe-logo.png',
                        allowRememberMe: false,
                        token: function (token) {
                            // You can access the token ID with `token.id`.
                            // Get the token ID to your server-side code for use.

                            instance.axiosPost('/paymentstripe', {
                                    token: token.id,
                                    bill: instance.newPoint
                                },
                                function (response) {
                                    $('#stripeModal').modal('hide');
                                },
                                function (error) {
                                    instance.stripeError = true;
                                });
                        },
                    });
                } else if (this.isActive == 2) {
                    paypal();
                }
            },
            // pay() {
            //     if (this.isActive == 1) {
            //       Stripe.createToken({
            //         number: this.cardNumber,
            //         cvc: this.cardCvc,
            //         exp_month: this.expiryMonth,
            //         exp_year: this.expiryYear
            //       }, this.stripeResponseHandler);
            //     }
            // },
            // stripeResponseHandler(status, response) {
            //     if (response.error) {
            //         this.stripeError = true;
            //     } else {
            //         /* token contains id, last4, and card type */
            //         let token = response['id'];
            //             this.axiosPost('/stripe-action', {
            //                     token: token,
            //                     bill: this.newPoint
            //                 },
            //                 function (response) {
            //                     console.log(response);
            //                 },
            //                 function (response) {
            //                     console.log('error: ' + response);
            //                     this.stripeError = true;
            //                 },
            //         );
            //     }
            // }
            // Payment Methods
            paypal() {
                let instance = this;
                setTimeout(function () {
                    paypal.Buttons({
                        createOrder: function (data, actions) {
                            return actions.order.create({
                                purchase_units: [{
                                    amount: {
                                        value: instance.newPoint
                                    }
                                }]
                            });
                        },
                        onApprove: function (data, actions) {
                            return actions.order.capture().then(function (details) {
                                instance.axiosPost('/paypal-transaction-complete', {orderID: data.orderID},
                                    function (response) {
                                        if (response.data == 'success') {
                                            $('#paypalModal').modal('hide');
                                        } else {
                                            instance.paypalError = true;
                                            $('#paypalModal').modal('hide');
                                        }
                                    }, function (error) {
                                        instance.paypalError = true;
                                        $('#paypalModal').modal('hide');
                                    });

                            });
                        },
                        onError: function (err) {
                            if (instance.paypalError) instance.paypalError = false;
                        },

                    }).render('#paypal-button-container');
                });
            }
        },
        computed: {
            price: function() {
                let point = this.newPoint;
                return '$' + Number(this.newPoint);
            }
        }
    }
</script>
<style>
    .payment-methods-btn {
        width: auto;
        margin-right: 1rem;
        border-radius: 2rem;
        padding: 0.5rem 1.7rem!important;
    }
</style>