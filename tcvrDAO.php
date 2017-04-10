<?php
// file tcvrDAO.php
// //////////////////////////////////////////////////////////////
// testing pdo
// pja 4-24-2016 added seekTCVR, clearTCVR
// pja 2-15-2015 decoded output of read
// pja 12-14-2014 blanked echo and print for production
// pja 11-17-2014 cloned from ixxMS original
// fns:
    // createTCVR, 
    // CreateIX, getIX, 
    // clearTCVR -- drop all records
    // readTCVR, writeTCVR, seekTCVR
// -- assume ixxMS has been included
// //////////////////////////////////////////////////////////////

function CreateTCVR($dbh,$Tname) 
{
        $stmt = 'create table if not exists ' . $Tname . 'IXX numeric(12),   T varchar(1000) , C varchar(1000), V varchar(10000), R varchar(1000) , primary key (IXX) ) ;' ;
        executeDB($dbh, $stmt);
} // CreateTCVR


function CreateIX($dbh)
{
    $s2 = 'create table if not exists IXX ( v varchar(1), IX numeric, primary key( v ));' ;# constant key v = 'v'
    executeDB($dbh,$s2);
    // set ix to 0
    $s3 = "insert into IXX values ( 'v' , 0 ) ; " ;
    executeDB($dbh,$s3);
} //

function clearTCVR($dbh) 
{
    $sq = "Truncate TABLE TCVR;";
    executeDB($dbh,$sq);
} // end clearTCVR 

function writeTCVR( $dbh,  $T , $C , $V , $R ) 
{
   include_once 'llogg.php';
    // write to TCVR table; returns num rows written 0 = error
// removed ixxx parameter 
// removed tname parameter - fixed at TCVR
    $t = SQin($T); // protect elements
    $c = SQin($C); 
    $v = SQin($V);
    $r = SQin($R); 
    // see if this exists y-delete befor write;  no-write
    $ans2 = 'select * from TCVR ' ;
    $ans2 .= " where T = '" . $t . "'";
    $ans2 .= " and C = '" . $c . "'";
    $ans2 .= " and R = '" . $r . "'";
    
    $ans = readDB($dbh, $ans2);

logg("<br/> and count " . count($ans) . ")" );
    if (count($ans) == 0)
    {
        $ixx = getIX($dbh);
        $ic = "insert into TCVR  values (";
        $ic .= "'" . $ixx . "',";
        $ic .= "'" . $t . "',";
        $ic .= "'" . $c . "',";
        $ic .= "'" . $v . "',";
        $ic .= "'" . $r . "' );";
        logg("<br />writeTCVR ic=(" . $ic . ")" );
        writeDB($dbh, $ic);
    } else { // delete and add
        $ic = "delete from TCVR  where";
        $ic .= "T = '" . $t . "',";
        $ic .= "and C = '" . $c . "',";
        $ic .= "and R = '" . $r . "' ;";
        logg("<br />delete ic=(" . $ic . ")" );
        executeDB($dbh,$ic);
        $ixx = getIX($dbh);
        $ic = "insert into TCVR  values (";
        $ic .= "'" . $ixx . "',";
        $ic .= "'" . $t . "',";
        $ic .= "'" . $c . "',";
        $ic .= "'" . $v . "',";
        $ic .= "'" . $r . "' );";
        logg("<br />writeTCVR ic=(" . $ic . ")" );
        writeDB($dbh, $ic);
    }// endif
}

function readTCVR($dbh, $Tname , $tag, $T,$C,$V,$R) 
{
    include_once 'llogg.php';
    // read according to values 
     // returns array [row#][ColName]=val
    $sq = " Select " . $tag . " from " . $Tname . " ";
    $wh = " where ";
    if ( $T != "" ) {
        $sq = $sq . $wh . " T = '" . SQin($T) . "' ";
        $wh = " and ";
    } //endif
    if ( $C != "") {
       $sq = $sq . $wh . " C = '" . SQin($C) . "' ";
       $wh = " and ";
    }
    if ( $V != "") {
       $sq = $sq . $wh . " V = '" . SQin($V) . "' ";
       $wh = " and ";
    }
    if ( $R != "") {
       $sq = $sq . $wh . " R = '" . SQin($R) . "' ";
       $wh = " and ";
    }
    $sq = $sq . " ; ";
    $sm2 = readDB($dbh,$sq);
    $sm2 = SQout($sm2); // decode output
    logg("<br /> sq = (" . $sq . ") <br />" );
    // $sm2 <- result array
    return($sm2); // return set w/o conversion use converter on each element
} // end readTCVR

function seekTCVR($dbh, $T,$C,$V,$R) 
{
    include_once 'llogg.php';
    // PJA original 4-24-2016
    // fixed on table named = TCVR
    // read according to values seek pattern
     // returns array [row#][ColName]=val
     // determine seek pattern TCVR and protect input
     $sp = 0;
     if ( $T != "" ) {
       $sp = $sp + 8;
       $T = SQin($T);
    } //endif
    if ( $C != "" ) {
       $sp = $sp + 4;
       $C = SQin($C);
    } //endif
    if ( $V != "" ) {
       $sp = $sp + 2;
       $V = SQin($V);
    } //endif
    if ( $R != "" ) {
       $sp = $sp + 1;
       $R = SQin($R);
    } //endif
    // develope select by pattern
    /*
    search
0 — => [t] == tables in database 
1 r => [t] == tables using r (side-car, etc)
2 v => [c] ** == no conflict w/ meta 
4 c => [v] == domain of c
6 cv => [r] == indexes where c=v
8 t => [r] == indexes of t
9 tr => [c] == structure of t at r
12 tc => [r] ** == no conflict w/ meta
13 tcr => v == value of c at r in t
14 tcv => [r] == indexes where c=v in t
15 tcvr => tcvr/0 == existence test
    */
    switch($sp) {
        case 0:
            // 0 — => [t] == tables in database 
            $sq = 'select distinct(T) from TCVR;'; 
            break;
        case 1:
            // 1 r => [t] == tables using r (side-car, etc)
            $sq = "select distinct(T) from TCVR where R ='" . $R ."' ;"; 
            break;
        case 2: // 2 v => [c] ** == no conflict w/ meta == where used
            $sq = "select distinct(C) from TCVR where V ='" . $V . "' ;";
            break;
        case 4: //  c => [v] == domain of c
            $sq = "select distinct(V) from TCVR where C = '" . $C . "' ;";
            break;
        case 6: // cv => [r] == indexes where c=v
            $sq = "select distinct(R) from TCVR where C = '" . $C ."' and  V = '" . $V ."'  ; ";
            break;
        case 8: //  t => [r] == indexes of t
            $sq = "select distinct(R) from TCVR where T = '" . $T . "' ;";
            break;
        case 9: //  tr => [c] == structure of t at r
            $sq = "select distinct(C) from TCVR where T = '" . $T . "' and R = '" . $R . "' ;";
            break;
        case 12: //  tc => [r] ** == no conflict w/ meta where used
             $sq = "select distinct(R) from TCVR where T = '" . $T . "' and C = '" . $C . "' ;";
            break;
        case 13: // tcr => v == value of c at r in t
            $sq = "select V from TCVR where T = '" . $T . "' and C = '" . $C . "' and R = '" . $R . "' ;";
            break;
        case 14: // tcv => [r] == indexes where c=v in t
            $sq = "select distinct(R) from TCVR where T = '" . $T . "' and C = '" . $C . "' and V = '" . $V . "' ;";
            break;
        case 15: // tcvr => 1/0 == existence test
            $sq = "select count(*) from TCVR where T = '" . $T . "' and C = '" . $C . "' and V = '" . $V . "' and R = '" . $R . "' ;"; 
            break;
    } // end switch
    
    $sm2 = readDB($dbh,$sq);
    $sm2 = SQout($sm2); // decode output
    logg("<br /> sq = (" . $sq . ") <br />" );
    // $sm2 <- result array
    return($sm2); // return set 
} // end seekTCVR

function getIX($dbh) {
    include_once 'llogg.php';
    // updates ixx.ix returns burned number
    // get ixx.ix
    // use dao
    $sql = "select ix from IXX where v = 'v' ;" ;
    $ixx = readDB($dbh,$sql);
    //echo('<br /> ixx ');
    loggr($ixx);
    //echo('<br /> --ixx ');
    $ixxx = $ixx[0]['ix'];
    

    // add 1
    $ix3 = $ixxx + 1;
    // write ixx.ix
    $q1 = "UPDATE IXX SET  v = 'v' , ix = " . $ix3 .  "  where v = 'v' ; ";
    writeDB($dbh,$q1);
    return($ixx[0]['ix']);
}

/*
// test bed
include 'ixxMS.PHP';
include 'dbc.php';
$dbh = openDB($dbna,$una,$pwd) ;
$ixxx = getIX($dbh);
$T = 'testT';
$R = 'R1';
$C = 'C1'; $V = 'V1';
writeTCVR( $dbh, $ixxx , $T , $C , $V , $R );
closeDB($dbh);
*/


?>
