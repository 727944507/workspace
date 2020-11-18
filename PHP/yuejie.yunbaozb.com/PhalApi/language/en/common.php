<?php
//\PhalApi\T
//例： '你好，{n}'=>'hello,{n}'
return array(
	'您的登陆状态失效，请重新登陆！'=>'Your logging status is invalid, please login again!',
	'信息错误'=>'Information error',
	'签名错误'=>'Signature error',
	'该账号已被禁用'=>'This account has been disabled',
	'余额不足'=>'Insufficient balance',

	'操作成功'=>'Operation succeeded',
	'发送失败'=>'Delivery failed',

    '半小时'=>'Half an hour',
    '小时'=>'Hours',
    '局'=>'Ju',
    '张'=>'Zhang',
    '次'=>'Ci',
    '幅'=>'Fu',
    '部'=>'Bu',
    '首'=>'Shou',


    /* functions */    
    '验证码'=>'Verification code',
	'您的验证码是：{$code}。请不要把验证码泄露给其他人'=>'Your verification code is: {$code}. Please don’t disclose the verification code to others',
    '用户不存在'=>'The user does not exist',
    '摩羯座'=>'Capricorn',
    '水瓶座'=>'Aquarius',
    '双鱼座'=>'Pisces',
    '白羊座'=>'Aries',
    '金牛座'=>'Taurus',
    '双子座'=>'Gemini',
    '巨蟹座'=>'Cancer',
    '狮子座'=>'Leo',
    '处女座'=>'Virgo',
    '天秤座'=>'Libra',
    '天蝎座'=>'Scorpio',
    '射手座'=>'Sagittarius',

    '1分钟前'=>'1 minute ago',
    '{n}分钟前'=>'{n} minutes ago',
    '{n}小时前'=>'{n} hours ago',
    '{n}天前'=>'{n} days ago',
    '今天'=>'Today',
    '明天'=>'Tomorrow',
    '后天'=>'The day after tomorrow',
    '昨天'=>'Yesterday',
    '前天'=>'The day before yesterday',

    '邮箱格式错误'=>'Mailbox format error',

    /* Cash */    
    '提现成功'=>'Successful withdrawal',
    '请选择提现账号'=>'Please select withdrawal account',
    '请输入有效的提现金额'=>'Please type in a valid withdrawal amount',
    '不在提现期限内，不能提现'=>'Not within withdrawal period, and withdrawal is not allowed',
    '提现最低额度为{n}元'=>'The minimum withdrawal amount is {n} yuan',
    '每月只可提现{n}次,已达上限'=>'Only {n} times of withdrawals can be made every month, and the limit has been reached',
    '提现账号信息不正确'=>'Incorrect withdrawal account information',
    '提现失败，请重试'=>'Withdrawal failed, please try again',

    '添加成功'=>'Added successfully',
    '银行名称不能为空'=>'It cannot be empty for the bank name',
    '账号不能为空'=>'It cannot be empty for the account number',
    '添加失败，请重试'=>'Fail to add, please try again',

    '删除成功'=>'Deleted successfully',
    '删除失败，请重试'=>'Fail to delete, please try again',

    /* Charge */    
    'Google Pay'=>'Google Pay',
    '苹果支付'=>'Apple Pay',
    '余额支付'=>'Balance payment',
    '订单生成失败'=>'Order generation failed',

    /* Comment */    
    '评价成功'=>'Comment completed',
    '您已经评价过了'=>'You have already commented',
    '订单无效，无法评价'=>'You are unable to comment for the order is invalid',
    '最多选择三个标签'=>'Select up to three tags',
    '评价失败，请重试'=>'Comment failed, please try again',

    /* Home */    
    '请输入关键词'=>'Please type in keyword',

    /* login */    
    '请输入邮箱'=>'Please type in mailbox',
    '请输入密码'=>'Please type in password',
    '账号或密码错误'=>'Incorrect account number or password',

    '请输入验证码'=>'Please type in verification code',
    '请输入确认密码'=>'Please type in confirmation password',
    '请先获取验证码'=>'Please get the verification code first',
    '验证码已过期，请重新获取'=>'Verification code has expired, please get one again',
    '邮箱错误'=>'Mailbox error',
    '验证码错误'=>'Verification code error',
    '两次密码不一致'=>'Two passwords do not match',
    '密码为6-20位数字和字母组合'=>'The password is a combination of 6-20 digits and letters',

    '注册成功'=>'Successful Registration',
    '陪玩用户'=>'Users playing with others',

    '发送成功，请注意查收'=>'Send successfully, please check',
    '请输入正确的邮箱'=>'Please type in the correct mailbox',
    '该账号已注册'=>'This account is already registered',
    '验证码5分钟有效，请勿多次发送'=>'Verification code is valid for 5 minutes, please do not send for many times',
    '验证码为：{n}'=>'Verification code is: {n}',

    '该账号未注册'=>'This account is not registered',

    /* Orders */    
    '已移除'=>'Removed',

    '不能给自己下单'=>'You cannot place orders for yourself',
    '请选择正确的时间'=>'Please select the correct time',
    '备注不能超过50字'=>'Remarks cannot exceed 50 words',
    '该技能对方未认证或未开启'=>'The skill is not authenticated or opened by the other side',
    '{n}给你下了订单'=>'{n} placed an order for you',
    '订单已收到，会尽快确认'=>'The order has been received and will be confirmed as soon as possible',

    '订单信息有误'=>'Error in order information',

    '该订单不能取消'=>'This order cannot be canceled',
    '请选择原因'=>'Please select reasons',
    '订单：您取消了一个订单'=>'Order: you canceled an order',
    '订单：您取消了一个订单，费用{n}已退回'=>'Order: you canceled an order and the fee {n} has been returned',
    '订单：很抱歉，用户取消了您的订单哦～'=>'Order: sorry, the user canceled your order~',

    '该订单未付款，无法接单'=>'This order has not been paid and cannot be accepted',
    '大神通过了您的订单，快去让大神带起飞吧'=>'The master passed your order, go and let the master carry you',

    '订单已处理，无法操作'=>'Order processed, unable to operate',
    '订单：很抱歉，大神没通过订单哦'=>"Order: I'm sorry, the master didn't pass the order",
    '订单：很抱歉，大神没通过订单哦，费用{n}已退回'=>"Order: I'm sorry, great god didn't pass the order. the fee {n} has been returned",

    '对方还未接单，无法完成'=>'The order has not been received yet by the other side, and the it cannot be completed',
    '接单：订单已经结束了，收入{n}，您可以给用户评价哦'=>'Receipt: the order has been completed and the income is {n}. You can give make a comment on the user',

    /* Skill */
    '请等待技能认证通过'=>'Please wait for the pass of the skill certification',
    '请先设置价格'=>'Please set the price first',

    '请选择正确的价格'=>'Please select the correct price',
    '介绍最多30个字'=>'Introduction up to 30 words',

    '技能不存在'=>'Skill does not exist',
    '对方未认证此技能'=>'The other party has not authenticated this skill',

    /* User */    
    '订单中心'=>'Order center',
    '我的钱包'=>'My wallet',
    '我的技能'=>'My skills',
    '申请大神'=>'Apply for being the master',
    '实名认证'=>'Real name authentication',
    '设置'=>'Settings',

    '请上传头像'=>'Please upload head portrait',
    '请设置您的昵称'=>'Please set your nickname',
    '请选择出生日期'=>'Please select the date of birth',
    '请选择您的性别'=>'Please select your gender',
    '昵称最多10个字'=>'Nickname can be up to 10 words',
    '昵称已存在'=>'Nickname already exists',
    '最多选择五个兴趣'=>'Select up to five interests',

    '不能关注自己'=>'You cannot follow yourself',
    '取消成功'=>'Cancellation Successful',
    '关注成功'=>'Focus on success',
    
    '来自{n}'=>'From {n}',
    '女生'=>'Girls',
    '男生'=>'Boys',
    '{n}后'=>'After {n}',
    '喜欢{n}'=>'Like {n}',
    '从事{n}'=>'Engaged in {n}',
    '毕业于{n}'=>'Graduated from {n}',
    
    
);
