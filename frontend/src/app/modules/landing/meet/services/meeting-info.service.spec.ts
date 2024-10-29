import { TestBed } from '@angular/core/testing';

import { MeetingInfoService } from './meeting-info.service';

describe('MeetingInfoService', () => {
  let service: MeetingInfoService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(MeetingInfoService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
