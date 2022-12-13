import { toastController } from '@ionic/vue'

export default async function (options) {
    const toast = await toastController
        .create({
            header: options?.title,
            message: options.message,
            duration: 3000,
            position: 'top',
            cssClass: 'overlay-hide ' + options?.class,
            buttons: [
                {
                    role: 'cancel',
                    handler: () => {
                        toast.onDidDismiss()
                    }
                }
            ]
        })
    return toast.present();
}
