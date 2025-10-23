import { paginationOptsValidator } from "convex/server";
import { mutation, query } from "./_generated/server";
import { v } from "convex/values";



export const getOrders = query({
    args:{
        paginationOpts: paginationOptsValidator
    },
    handler: async (ctx, args) => {
        const data = await ctx.db
        .query("orders")
        .order("desc")
        .paginate(args.paginationOpts)
        return data
    }
});



export const addOrder = mutation({
    args: {
        items: v.array(
            v.object({
                userId: v.string(),
                itemId: v.string(),
                itemName: v.string(),
                quantity: v.number(),
            })
        )
    },
    handler: async (ctx, args) => {
        const {items} = args
        for (const item of items){
            await ctx.db.insert("orders", {
                userId: item.userId,
                itemId: item.itemId,
                itemName: item.itemName,
                quantity: item.quantity,
                orderDate: new Date().toISOString(),
                status: "pending",
            });
        }
    }
});
