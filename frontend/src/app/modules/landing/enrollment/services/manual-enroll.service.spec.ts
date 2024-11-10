import { TestBed } from '@angular/core/testing';

import { ManualEnrollService } from './manual-enroll.service';

describe('ManualEnrollService', () => {
  let service: ManualEnrollService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(ManualEnrollService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
