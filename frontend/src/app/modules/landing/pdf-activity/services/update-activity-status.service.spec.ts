import { TestBed } from '@angular/core/testing';

import { UpdateActivityStatusService } from './update-activity-status.service';

describe('UpdateActivityStatusService', () => {
  let service: UpdateActivityStatusService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(UpdateActivityStatusService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
