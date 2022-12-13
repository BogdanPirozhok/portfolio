import copy from 'copy-to-clipboard';
import toast from '@/helpers/toast';

export default function (options) {
    copy(options.value)

    toast({
        message: options.message,
        class: 'success'
    })
}
