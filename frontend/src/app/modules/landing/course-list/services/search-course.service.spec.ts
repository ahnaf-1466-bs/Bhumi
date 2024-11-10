import { TestBed } from '@angular/core/testing';

import { SearchCourseService } from './search-course.service';

describe('SearchCourseService', () => {
  let service: SearchCourseService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(SearchCourseService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
