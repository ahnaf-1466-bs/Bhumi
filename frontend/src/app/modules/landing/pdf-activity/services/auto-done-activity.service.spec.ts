import { TestBed } from '@angular/core/testing';

import { AutoDoneActivityService } from './auto-done-activity.service';

describe('AutoDoneActivityService', () => {
  let service: AutoDoneActivityService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AutoDoneActivityService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
