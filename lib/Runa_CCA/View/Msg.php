<?php
/**
 * Message Class
 *
 * @author rutoru
 * @package Runa-CCA
 */
namespace Runa_CCA\View;

class Msg {

    /**
     * Constants for Messages
     */
    const TITLE_LOGINERR  = "警告！";
    const MSG_LOGINERR    = "ログインIDまたはパスワードが違います。";
    const TITLE_LOGIN     = "ログイン完了";
    const MSG_LOGIN       = "ログインしました。";
    const TITLE_LOGOUT    = "おつかれさまでした";
    const MSG_LOGOUT      = "ログアウトしました。";
    const TITLE_ERROR     = "エラー";
    const MSG_ERROR       = "システム管理者に連絡してください。";
    const TITLE_TW_ERROR  = "Twilioエラー";
    const MSG_TW_ERROR    = "Twilioエラー。システム管理者に連絡してください。";
    const TITLE_NOAUTH    = "権限NG";
    const MSG_NOAUTH      = "権限がありません。再ログインしてください。";
    const TITLE_NOTFOUND  = "不正アクセス";
    const MSG_NOTFOUND    = "不正なアクセスです。";
    const TITLE_CONFLOGIN = "管理画面";
    const MSG_CONFLOGIN   = "管理画面です。メニューから管理項目を選択してください。";
    const TITLE_OPLIST    = "オペレータ管理画面";
    const MSG_OPLIST      = "情報を変更する際はラジオボタンで選択後に表示ボタンを押してください。";
    const ALERT_WARNING   = "alert-warning";
    const ALERT_DANGER    = "alert-danger";
    const ALERT_SUCCESS   = "alert-success";
    const ALERT_INFO      = "alert-info";
    const TITLE_WARNING   = "注意";
    const TITLE_DANGER    = "警告";
    const TITLE_SUCCESS   = "成功！";
    const TITLE_INFO      = "情報";
    const MSG_WARNING     = "注意です。";
    const MSG_DANGER      = "警告です。";
    const MSG_SUCCESS     = "成功です。";
    const MSG_INFO        = "情報です。";
    
}