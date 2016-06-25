temp=`getopt -o ab:c:: --long a-long,b-long:,c-long:: \
     -n 'example.bash' -- "$@"`
if [ $? != 0 ] ; then echo "terminating..." >&2 ; exit 1 ; fi
eval set -- "$temp"

#经过getopt的处理，下面处理具体选项。
while true ; do
        case "$1" in
                -a|--a-long) echo "option a" ; shift ;;
                -b|--b-long) echo "option b, argument \`$2'" ; shift 2 ;;
                -c|--c-long)
                        # c has an optional argument. as we are in quoted mode,
                        # an empty parameter will be generated if its optional
                        # argument is not found.
                        case "$2" in
                                "") echo "option c, no argument"; shift 2 ;;
                                *)  echo "option c, argument \`$2'" ; shift 2 ;;
                        esac ;;
                --) shift ; break ;;
                *) echo "internal error!" ; exit 1 ;;
        esac
done
echo "remaining arguments:"
for arg do
   echo '--> '"\`$arg'" ;
done
