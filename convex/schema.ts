import { defineSchema, defineTable } from "convex/server";
import { v } from "convex/values";



export default defineSchema({
  items: defineTable({
    userId: v.string(),
    imageUrl: v.string(),
    itemName: v.string(),
    quantity: v.string(),
    itemDescription: v.string(),
    price: v.string(),
    category: v.string(),
    createdAt: v.string(),
  }),


  orders: defineTable({
    userId: v.string(),
    itemId: v.string(),
    itemName: v.string(),
    quantity: v.number(),
    orderDate: v.string(),
    status: v.string(),
  }),



});



