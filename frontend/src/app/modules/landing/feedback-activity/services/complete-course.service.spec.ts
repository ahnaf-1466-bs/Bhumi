import { TestBed } from '@angular/core/testing';

import { CompleteCourseService } from './complete-course.service';

describe('CompleteCourseService', () => {
  let service: CompleteCourseService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(CompleteCourseService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
