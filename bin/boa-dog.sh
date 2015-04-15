#/bin/sh
COUNT_R=0
date > /var/run/boa_dog.rlt
echo "*************************** ">>/var/run/boa_dog.rlt
while [ 1 ];
do
        sleep 15

		boa_status=`ps aux |grep " boa " | grep -v grep`
        busy_status=`echo $boa_status | grep " S "`

        if [ "$boa_status" = "" -o "$busy_status" = "" ]; then
                COUNT_R=`expr $COUNT_R + 1`
        elif [ "$COUNT_R" -gt "0" ]; then
                COUNT_R=`expr $COUNT_R - 1`
        fi

        if [ "$COUNT_R" -gt 10 ]; then
				killall boa
				sleep 3
                boa &
                date >> /var/run/boa_dog.rlt
                echo COUNT_T=$COUNT_R, boa_status=$boa_status >>/var/run/boa_dog.rlt
                echo restart boa >> /var/run/boa_dog.rlt
                echo "*************************** ">>/var/run/boa_dog.rlt
                COUNT_R=0
        fi

		#echo boa_status=$boa_status
        #echo busy_status=$busy_status
		#echo COUNT_R=$COUNT_R
done

