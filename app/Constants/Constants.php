<?php


namespace App\Constants;


class Constants
{
    const ACCESS_ERROR = 'شما به این بخش دسترسی ندارید';
    const FILE_NOT_FOUND_ERROR = 'فایل یافت نشد';
    const USER_NOT_FOUND_ERROR = 'کاربر یافت نشد';
    const INVALID_PHONE_NUMBER_ERROR = 'شماره تماس اشتباه است';
    const INVALID_EMAIL_ERROR = 'ایمیل اشتباه است';
    const INVALID_LOGIN_ERROR = 'ایمیل یا کلمه عبور اشتباه است';
    const INVALID_EMAIL = 'امکان ثبت این ایمیل وجود ندارد است';
    const UNDEFINED_ERROR = 'متاسفانه خطایی رخ داده است';
    const LADDERING_TYPE_ERROR = 'نوع عملیات نردبان ضروری است';
    const LADDERING_COUNT_ERROR = 'ظرفیت نردبان در 48 ساعت گذشته ی شما کمتر از تعداد نمونه کارهای انتخاب شده برای نردبان می باشد. برای افزایش ظرفیت می توانید پنل خود رو ارتقا دهید!';
    const LADDERING_PRICE_ERROR = 'هزینه ی نردبان از موجودی سکه ی شما بیشتر است. لطفا سکه های خود را افزایش دهید!';
    const INVALID_PASSWORD_ERROR = 'پسورد اشتباه است';
    const INVALID_OLD_PASSWORD_ERROR = 'پسورد قدیمی اشتباه است';
    const USER_REGISTERED_BEFORE_ERROR = 'این ایمیل پیشتر ثبت شده است';
    const INVALID_OTP_CODE_ERROR = 'کد اشتباه است یا منقضی شده است';
    const NOT_ENOUGH_COINS = 'موجودی کافی نیست';
}
