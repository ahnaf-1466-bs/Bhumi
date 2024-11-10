import { TestBed } from '@angular/core/testing';

import { FooterLinkApiService } from './footer-link-api.service';

describe('FooterLinkApiService', () => {
  let service: FooterLinkApiService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(FooterLinkApiService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
