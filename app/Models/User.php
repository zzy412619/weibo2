<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;//消息通知相关功能的引用

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // 当我们需要对用户密码或其它敏感信息在用户实例通过数组或 JSON 显示时进行隐藏，则可使用 hidden 属性：
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //用户头像
     public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }
    // creating 用于监听模型被创建之前的事件，created 用于监听模型被创建之后的事件。
    // 激活令牌
    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
        
    }
    //用户发布的多条微博
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }
    //将用户发布过的所有微博从数据库中取出
    public function feed()
    {
        return $this->statuses()
                    ->orderBy('created_at', 'desc');
    }
    //followers 来获取粉丝关系列表
    public function followers()
    {
        return $this->belongsToMany(User::Class,'followers','user_id','follower_id');
    }
    // followings 来获取用户关注人列表
    public function followings()
    {
        return $this->belongsToMany(User::Class,'followers','user_id','follower_id');
    }

    //关注
    public function follow($user_ids)
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids,false);
    }

    //取消关注]
    public function unfollow()
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }
    //判断当前登录的用户 A 是否关注了用户 B
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }

}