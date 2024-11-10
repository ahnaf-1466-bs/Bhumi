import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'timer',
})
export class TimerPipe implements PipeTransform {
    transform(value: any, ...args: any[]) {
        return `${Math.round(value / 60)} mins ${value % 60} secs`;
    }
}
