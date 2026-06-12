<?php
declare(strict_types=1);

namespace app\utils;

/**
 * 加密工具类
 */
class Encrypt
{
    /**
     * 密码哈希加密
     * @param string $password 原始密码
     * @return string 加密后的密码
     */
    public static function passwordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 验证密码
     * @param string $password 输入的密码
     * @param string $hash 存储的哈希值
     * @return bool 是否匹配
     */
    public static function passwordVerify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * 生成随机字符串
     * @param int $length 长度
     * @return string 随机字符串
     */
    public static function randomString(int $length = 32): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $result;
    }

    /**
     * 生成验证码
     * @param int $length 长度
     * @return string 验证码
     */
    public static function generateCode(int $length = 4): string
    {
        $chars = '0123456789';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $result;
    }
}
