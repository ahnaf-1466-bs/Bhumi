import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WhatWillYouLearnComponent } from './what-will-you-learn.component';

describe('WhatWillYouLearnComponent', () => {
  let component: WhatWillYouLearnComponent;
  let fixture: ComponentFixture<WhatWillYouLearnComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ WhatWillYouLearnComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(WhatWillYouLearnComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
