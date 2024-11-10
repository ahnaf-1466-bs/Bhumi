import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';
@Component({
  selector: 'incomplete-modal',
  templateUrl: './incomplete-modal.html',
})
export class IncompleteModal {
  constructor(@Inject(MAT_DIALOG_DATA) public data: { message: string }) {

  }
}