import { Injectable } from '@angular/core';
import { BehaviorSubject, Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ActivityCompletionService {
  // private activitySubject = new BehaviorSubject<string>('initial value'); // Use BehaviorSubject if you need an initial value
  private activitySubject = new Subject<number>(); // Use Subject for simpler notification

  activityStatus$ = this.activitySubject.asObservable();

  updateActivityStatus(activityId: number) {
    this.activitySubject.next(activityId);
  }
}
