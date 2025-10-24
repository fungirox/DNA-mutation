import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Api } from '../../services/api';
import { response } from 'express';

@Component({
  selector: 'app-dna-checker',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './dna-checker.html',
  styleUrl: './dna-checker.css'
})
export class DnaChecker implements OnInit {

  constructor(private api: Api) { }

  result: any = null;
  stats: any = null;
  list: any = null;

  ngOnInit(): void {
    this.loadStats();
    this.loadList();
  }

  loadStats(): void {
    this.api.getStats().subscribe({
      next: (response) => {
        this.stats = response;
      },
      error: (error) => {
        console.error('Error loading stats:', error);
      }
    });
  }

  loadList(): void {
    this.api.getList().subscribe({
      next: (response) => {
        this.list = response;
      },
      error: (error) => {
        console.error('Error loading list:', error);
      }

    });
  }
}
