// adminmyorders.js
import { supabase } from './src/supabaseClient.js';

document.addEventListener('DOMContentLoaded', async () => {
  const container = document.getElementById('orders-container');

  const { data: orders, error } = await supabase.from('purchase').select('*');

  if (error) {
    console.error('Error fetching orders:', error);
    container.innerHTML = '<p>Failed to load orders.</p>';
    return;
  }

  if (!orders || orders.length === 0) {
    container.innerHTML = '<p>No orders found.</p>';
    return;
  }

  orders.forEach(order => {
    const orderDiv = document.createElement('div');
    orderDiv.className = 'card card-body';
    const itemsHtml = `<li>Product: ${order.product}</li><li>Amount: ${order.amount}</li>`;
    
    orderDiv.innerHTML = `
      <h5 class="card-title">Order ID: ${order.id}</h5>
      <p class="card-text"><strong>Date:</strong> ${order.created_at}</p>
      <ul>${itemsHtml}</ul>
    `;

    container.appendChild(orderDiv);
  });
});
