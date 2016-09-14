<?php
class Reservation_TechnicianService
{
    /**
        * //取非工作时间
        * @param $array 工作时间数组
        * @param $day   日期 
        * @param string $compareTime 店铺营业时间
        * @return mixed
        */
        public static function getUnWork($array, $day, $compareTime = '')
        {
            $unixStartDay = strtotime($day); //当日0点
            $unixEndDay = strtotime($day) + 23 * 60 * 60 + 59 * 60 + 59; //当日23:59:59点
            $a = array();
            foreach ($array as $k => &$v)
            {
                if ($v['s_time_slot'])
                {
                    $count = count($v['s_time_slot']);

                    for ($c = 0; $c <= $count; $c++)
                    {
                        if ($c == 0)
                        {
                            if ($compareTime && $compareTime['s'] && date('H:i',$compareTime['s']) !='00:00')
                            {
                                $start = $compareTime['s'];
                            }
                            else
                            {
                                $start = $unixStartDay;
                            }
                            $a[$k][$c]['s'] = date('H:i', $start);
                        }
                        else
                        {
   
                            if($v['s_time_slot'][$c - 1]['e'] == '24:00')
                            {
                                $a[$k][$c]['s'] = '23:59'; //24:00
                            }
                            else
                            {
                                $a[$k][$c]['s'] = $v['s_time_slot'][$c - 1]['e'];
                            }

                        }

                        if ($c == count($v['s_time_slot']))
                        {
                            if ($compareTime && $compareTime['e'] && date('H:i',$compareTime['e']) !='00:00')
                            {
                                $end = $compareTime['e'];
                            }
                            else
                            {
                                $end = $unixEndDay;
                            }

                            $a[$k][$c]['e'] = date('H:i', $end);
                        }
                        else
                        {
                            $a[$k][$c]['e'] = $v['s_time_slot'][$c]['s'];

                        }

                    }
                    $array[$k]['unwork'] = $a[$k];
                }
                else
                {
                    if($compareTime) //店铺有设营业时间,以店铺为准
                    {
                        $array[$k]['unwork'] = array(array('s'=>date('H:i',$compareTime['s']),'e'=>date('H:i',$compareTime['e'])));
                    }
                    else
                    {
                        $array[$k]['unwork'] = array(array('s'=>'00:00','e'=>'24:00'));
                    }

                }

            }
            return $array;
        }
}