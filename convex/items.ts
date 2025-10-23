import { paginationOptsValidator } from "convex/server";
import { mutation, query } from "./_generated/server";
import { v } from "convex/values";



export const generateUploadUrl = mutation({
  handler: async (ctx) => {
    return await ctx.storage.generateUploadUrl();
  },
});

export const addItem = mutation({
  args: { 
    userId: v.string(),
    imageUrl: v.id("_storage"),
    itemName: v.string(),
    quantity: v.string(),
    itemDescription: v.string(),
    price: v.string(),
    category: v.string(),
    createdAt: v.string(),

},
  handler: async (ctx, args) => {
    await ctx.db.insert("items", {
      userId: args.userId,
      imageUrl: args.imageUrl,
      itemName: args.itemName,
      quantity: args.quantity,
      itemDescription: args.itemDescription,
      price: args.price,
      category: args.category,
      createdAt: args.createdAt,
    });
  },
});


export const getItems = query({
  
    handler: async (ctx, args) => {
        const items = await ctx.db
        .query("items")
        .order("desc")
        .collect();
        return Promise.all(
      items.map(async (item) => ({
        ...item,
        // If the item is an "image" its `body` is an `Id<"_storage">`
        url: await ctx.storage.getUrl(item.imageUrl) 
        }))
    );
    }
});
