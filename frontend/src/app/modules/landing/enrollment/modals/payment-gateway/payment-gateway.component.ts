import { Component, EventEmitter, Output } from '@angular/core';

@Component({
    selector: 'app-payment-gateway',
    templateUrl: './payment-gateway.component.html',
    styleUrls: ['./payment-gateway.component.scss'],
})
export class PaymentGatewayComponent {
    @Output() dataEmitter = new EventEmitter<any>();

    paymentGateway: string = '';

    useSurjoPay() {
        this.paymentGateway = 'shurjopay';
        this.dataEmitter.emit(this.paymentGateway);
    }

    useBkash() {
        this.paymentGateway = 'bkash';
        this.dataEmitter.emit(this.paymentGateway);
    }
}
