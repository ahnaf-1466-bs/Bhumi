import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CompleteCourseService {

  private completeCourse = new Subject<void>();

  submit() {
    this.completeCourse.next();
  }

  getCourseCompletionEvent() {
    return this.completeCourse.asObservable();
  }
}
