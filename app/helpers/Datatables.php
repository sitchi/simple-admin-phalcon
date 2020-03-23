<?php
declare(strict_types=1);

namespace PSA\Helpers;

use Phalcon\Di\Injectable;

class Datatables extends Injectable
{

    public static function css()
    {
        $css = "<link href='/css/dataTables.bootstrap4.min.css' rel='stylesheet' type='text/css'>";
        $css .= "<link href='/css/responsive.bootstrap4.min.css' rel='stylesheet' type='text/css'>";
        return $css;
    }

    public static function js()
    {
        $js = "<script src='/js/jquery.dataTables.min.js'></script>";
        $js .= "<script src='/js/dataTables.bootstrap4.min.js'></script>";
        $js .= "<script src='/js/dataTables.responsive.min.js'></script>";
        $js .= "<script src='/js/responsive.bootstrap4.min.js'></script>";
        return $js;
    }

    public static function jsData()
    {
        $js = self::js();
        $js .= "<script type='text/javascript' language='javascript'>
        $('#dataTables').dataTable({
            responsive: true,
            stateSave: true,
            autoWidth: true,
            order: [[ 0, 'desc' ]],
        });
        </script>";
        return $js;
    }

    public static function jsAjax(string $url)
    {
        $js = self::js();
        $js .= "<script type='text/javascript' language='javascript'>
        $('#dataTables').dataTable({
            responsive: true,
            stateSave: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                type: 'POST',
                url: '" . $url . "'
            },
            order: [[ 0, 'desc' ]],
        });
        </script>";
        return $js;
    }

}

?>