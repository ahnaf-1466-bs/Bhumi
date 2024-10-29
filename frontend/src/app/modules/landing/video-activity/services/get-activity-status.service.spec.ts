import { TestBed } from '@angular/core/testing';

import { GetActivityStatusService } from './get-activity-status.service';

describe('GetActivityStatusService', () => {
  let service: GetActivityStatusService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(GetActivityStatusService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
