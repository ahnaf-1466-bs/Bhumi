import { TestBed } from '@angular/core/testing';

import { TeamsApiService } from './teams-api.service';

describe('TeamsApiService', () => {
  let service: TeamsApiService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(TeamsApiService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
